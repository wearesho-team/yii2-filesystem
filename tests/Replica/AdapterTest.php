<?php

namespace Wearesho\Yii\Filesystem\Tests\Replica;

use PHPUnit\Framework\TestCase;
use Wearesho\Yii\Filesystem;
use League\Flysystem\Config;
use Mockery\MockInterface;
use yii\base;

/**
 * Class AdapterTest
 * @package Wearesho\Yii\Filesystem\Tests\Replica
 * @coversDefaultClass \Wearesho\Yii\Filesystem\Replica\Adapter
 */
class AdapterTest extends TestCase
{
    /** @var Filesystem\Replica\Adapter */
    protected $adapter;

    /** @var Filesystem\AdapterInterface|MockInterface */
    protected $master;

    /** @var Filesystem\AdapterInterface|MockInterface */
    protected $slave;

    protected function setUp(): void
    {
        parent::setUp();

        $this->master = \Mockery::mock(Filesystem\AdapterInterface::class);
        $this->slave = \Mockery::mock(Filesystem\AdapterInterface::class);

        $this->adapter = new Filesystem\Replica\Adapter([
            'master' => $this->master,
            'slaves' => [
                $this->slave,
            ],
        ]);
    }

    public function callProvider()
    {
        return [
            'write' => ['write', true, 3],
            'read' => ['read', false, 1],
            'readStream' => ['readStream', false, 1],
            'getVisibility' => ['getVisibility', false, 1],
            'setVisibility' => ['setVisibility', true, 2],
            'getSize' => ['getSize', false, 1],
            'getMimetype' => ['getMimetype', false, 1],
            'getMetadata' => ['getMetadata', false, 1],
            'getTimestamp' => ['getTimestamp', false, 1],
            'rename' => ['rename', true, 2],
            'copy' => ['copy', true, 2],
            'deleteDir' => ['deleteDir', true, 1],
            'createDir' => ['createDir', true, 2],
            'has' => ['has', false, 1],
            'listContents' => ['listContents', false, 2],
        ];
    }

    /**
     * @dataProvider callProvider
     */
    public function testMethodDeligation($method, $useReplica, $arguments): void
    {
        $expected = 'result';
        $parameters = array_pad([], $arguments - 1, 'value');
        $parameters[] = new Config();

        $call = $this->master->shouldReceive($method)->twice();
        $call = call_user_func_array([$call, 'with'], $parameters);
        $call->andReturn(false, $expected);

        if ($useReplica === true) {
            $replicaCall = $this->slave->shouldReceive($method)->once();
            $replicaCall = call_user_func_array([$replicaCall, 'with'], $parameters);
            $replicaCall->andReturn($expected);
        }

        $this->assertFalse(call_user_func_array([$this->adapter, $method], $parameters));
        $this->assertEquals($expected, call_user_func_array([$this->adapter, $method], $parameters));
    }

    public function testGetSourceAdapter(): void
    {
        $this->assertEquals($this->master, $this->adapter->master);
    }

    public function testGetReplicaAdapter(): void
    {
        $this->assertEquals($this->master, $this->adapter->slaves[0]);
    }

    public function testMethodUpdateSourceWillNotUpdate(): void
    {
        $this->master->shouldReceive('has')->once()->andReturn(true);
        $this->master->shouldReceive('update')->once()->andReturn(false);

        $this->assertFalse(call_user_func_array([$this->adapter, 'update'], ['value', 'value', new Config()]));
    }

    public function testMethodUpdateSourceWillUpdateAndReplicaWillUpdate(): void
    {
        $this->master->shouldReceive('has')->once()->andReturn(true);
        $this->master->shouldReceive('update')->once()->andReturn(true);
        $this->slave->shouldReceive('has')->once()->andReturn(true);
        $this->slave->shouldReceive('update')->once()->andReturn(true);

        $this->assertTrue(call_user_func_array([$this->adapter, 'update'], ['value', 'value', new Config()]));
    }

    public function testMethodUpdateSourceWillUpdateAndReplicaWillWrite(): void
    {
        $this->master->shouldReceive('has')->once()->andReturn(true);
        $this->master->shouldReceive('update')->once()->andReturn(true);
        $this->slave->shouldReceive('has')->once()->andReturn(false);
        $this->slave->shouldReceive('write')->once()->andReturn(true);

        $this->assertTrue(call_user_func_array([$this->adapter, 'update'], ['value', 'value', new Config()]));
    }

    public function testMethodUpdateStreamSourceWillNotUpdate(): void
    {
        $this->master->shouldReceive('has')->once()->andReturn(true);
        $this->master->shouldReceive('updateStream')->once()->andReturn(false);

        $this->assertFalse(call_user_func_array(
            [$this->adapter, 'updateStream'],
            [
                'value',
                fopen('data:text/plain,value', 'r+'),
                new Config()
            ]
        ));
    }

    public function testMethodUpdateStreamSourceWillUpdateAndReplicaWillUpdate(): void
    {
        $this->master->shouldReceive('has')->once()->andReturn(true);
        $this->master->shouldReceive('updateStream')->once()->andReturn(true);
        $this->slave->shouldReceive('has')->once()->andReturn(true);
        $this->slave->shouldReceive('updateStream')->once()->andReturn(true);

        $this->assertTrue($this->adapter->updateStream('value', fopen('data:text/plain,value', 'r+'), new Config()));
    }

    public function testMethodUpdateStreamSourceWillUpdateAndReplicaWillWrite(): void
    {
        $this->master->shouldReceive('has')->once()->andReturn(true);
        $this->master->shouldReceive('updateStream')->once()->andReturn(true);
        $this->slave->shouldReceive('has')->once()->andReturn(false);
        $this->slave->shouldReceive('writeStream')->once()->andReturn(true);

        $this->assertTrue($this->adapter->updateStream('value', fopen('data:text/plain,value', 'r+'), new Config()));
    }

    public function testMethodUpdateStreamSourceWillWriteAndEnsureSeekableWillFail(): void
    {
        $this->markTestSkipped('disable due to update. must be reviewed.');

        stream_wrapper_register('test', NonSeekableStream::class);

        $this->master->shouldReceive('has')->once()->andReturn(true);
        $this->master->shouldReceive('updateStream')->once()->andReturn(true);
        $this->master->shouldReceive('readStream')->once()->andReturn(fopen('test://value', 'r+'));
        $this->slave->shouldReceive('has')->once()->andReturn(true);

        $this->assertFalse($this->adapter->updateStream('value', fopen('test://value', 'r+'), new Config()));

        stream_wrapper_unregister('test');
    }

    public function testMethodWriteStreamSourceWillWriteAndReplicaWillWrite(): void
    {
        $this->master->shouldReceive('writeStream')->once()->andReturn(true);
        $this->slave->shouldReceive('writeStream')->once()->andReturn(true);

        $this->assertTrue($this->adapter->writeStream('value', fopen('data:text/plain,value', 'r+'), new Config()));
    }

    public function testMethodWriteStreamSourceWillNotWrite(): void
    {
        $this->master->shouldReceive('writeStream')->once()->andReturn(false);

        $this->assertFalse($this->adapter->writeStream('value', fopen('data:text/plain,value', 'r+'), new Config()));
    }

    public function testMethodWriteStreamSourceWillWriteAndEnsureSeekableWillFail(): void
    {
        $this->markTestSkipped('disable due to update. must be reviewed.');

        stream_wrapper_register('fstest', NonSeekableStream::class);

        $this->master->shouldReceive('writeStream')->once()->andReturn(true);
        $this->master->shouldReceive('readStream')->once()->andReturn(fopen('data:text/plain,value', 'r+'));

        $this->assertFalse($this->adapter->writeStream('value', fopen('fstest://value', 'r+'), new Config()));

        stream_wrapper_unregister('fstest');
    }

    public function testMethodDeleteSourceWillNotDelete(): void
    {
        $this->master->shouldReceive('delete')->once()->andReturn(false);

        $this->assertFalse(call_user_func_array([$this->adapter, 'delete'], ['value']));
    }

    public function testMethodDeleteSourceWillDeleteAndReplicaWillDelete(): void
    {
        $this->master->shouldReceive('delete')->once()->andReturn(true);
        $this->slave->shouldReceive('has')->once()->andReturn(true);
        $this->slave->shouldReceive('delete')->once()->andReturn(true);

        $this->assertTrue(call_user_func_array([$this->adapter, 'delete'], ['value']));
    }

    public function testMethodDeleteSourceWillDeleteAndReplicaWillNotDelete(): void
    {
        $this->master->shouldReceive('delete')->once()->andReturn(true);
        $this->slave->shouldReceive('has')->once()->andReturn(false);

        $this->assertTrue(call_user_func_array([$this->adapter, 'delete'], ['value']));
    }

    public function testMethodBaseUrlWillReturnMasterBaseUrl(): void
    {
        $this->master->shouldReceive('getBaseUrl')->once()->andReturn('https://wearesho.com/');
        $this->assertEquals(
            'https://wearesho.com/',
            $this->adapter->getBaseUrl()
        );
    }

    public function testInvalidReplicaConfigurationWillThrownException(): void
    {
        $this->expectException(base\InvalidConfigException::class);
        $this->expectExceptionMessage("Replicas definition have to be an array");
        new Filesystem\Replica\Adapter([
            'master' => $this->master,
            'slaves' => $this->slave, // should be an array instead
        ]);
    }
}
