<?php

namespace Wearesho\Yii\Filesystem\Tests;

use PHPUnit\Framework\TestCase;
use Wearesho\Yii\Filesystem;

/**
 * Class FilesystemTest
 * @package Wearesho\Yii\Filesystem\Tests
 * @coversDefaultClass \Wearesho\Yii\Filesystem\Filesystem
 */
class FilesystemTest extends TestCase
{
    public function testConfiguringAdapter(): void
    {
        $fs = new Filesystem\Filesystem([
            'adapter' => [
                'class' => Filesystem\Local\Adapter::class,
                'config' => [
                    'class' => Filesystem\Local\Config::class,
                ],
            ],
        ]);

        $this->assertInstanceOf(
            Filesystem\Local\Adapter::class,
            $fs->getAdapter()
        );
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessage Adapter have to specified in config
     */
    public function testMissingAdapter()
    {
        new Filesystem\Filesystem([]);
    }

    public function testGettingUrl()
    {
        $fs = new Filesystem\Filesystem([
            'adapter' => [
                'class' => Filesystem\Local\Adapter::class,
                'config' => [
                    'class' => Filesystem\Local\Config::class,
                    'baseUrl' => 'https://wearesho.com/',
                ],
            ],
        ]);

        $this->assertEquals(
            'https://wearesho.com/404.html',
            $fs->getUrl('404.html')
        );
    }
}
