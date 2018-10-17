<?php

namespace Wearesho\Yii\Filesystem\Tests;

use PHPUnit\Framework\TestCase;
use Wearesho\Yii\Filesystem;
use League\Flysystem;
use yii\di;
use yii\console;

/**
 * Class BootstrapTest
 * @package Wearesho\Yii\Filesystem\Tests
 * @coversDefaultClass \Wearesho\Yii\Filesystem\Bootstrap
 */
class BootstrapTest extends TestCase
{
    public function testGetAdapterReference(): void
    {
        $bootstrap = new Filesystem\Bootstrap([
            'adapters' => [
                'a' => 'SomeClass',
                'b' => 'AnotherClass',
            ],
            'config' => [
                'class' => Filesystem\Config::class,
                'adapter' => 'a',
            ]
        ]);

        /** @var Filesystem\Config $config */
        $config = $bootstrap->config;
        $this->assertInstanceOf(Filesystem\Config::class, $config);
        $this->assertEquals('SomeClass', $bootstrap->getAdapterReference());

        $config->adapter = 'b';
        $this->assertEquals('AnotherClass', $bootstrap->getAdapterReference());
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessage Adapter unknown is not configured
     */
    public function testNotConfiguredAdapterReference(): void
    {
        $bootstrap = new Filesystem\Bootstrap([
            'adapters' => [],
            'config' => [
                'class' => Filesystem\Config::class,
                'adapter' => 'unknown',
            ],
        ]);
        $bootstrap->getAdapterReference();
    }

    public function testConfiguringContainer(): void
    {
        \Yii::$container = $container = new di\Container();
        $bootstrap = new Filesystem\Bootstrap([
            'adapters' => [
                'default' => [
                    'class' => Filesystem\Local\Adapter::class,
                    'config' => Filesystem\Local\Config::class,
                ],
            ],
            'config' => [
                'class' => Filesystem\Config::class,
                'adapter' => 'default',
            ],
        ]);
        $bootstrap->configure($container);

        /** @var Filesystem\Filesystem $fs */
        $fs = $container->get(Filesystem\Filesystem::class);
        $this->assertInstanceOf(Filesystem\Local\Adapter::class, $fs->getAdapter());
        $this->assertInstanceOf(Filesystem\Local\Config::class, $fs->getAdapter()->config);

        $this->assertTrue(
            $container->has(Flysystem\Filesystem::class)
        );
        $this->assertInstanceOf(
            Filesystem\Filesystem::class,
            $container->get(Flysystem\Filesystem::class)
        );

        $this->assertTrue(
            $container->has(Flysystem\AdapterInterface::class)
        );
        $this->assertInstanceOf(
            Filesystem\AdapterInterface::class,
            $container->get(Flysystem\AdapterInterface::class)
        );
        \Yii::$container = new di\Container();
    }

    public function testBootstrap(): void
    {
        \Yii::$container = new di\Container();
        $app = new console\Application([
            'id' => 'test',
            'basePath' => dirname(\Yii::getAlias('@runtime'))
        ]);
        $bootstrap = new Filesystem\Bootstrap([
            'adapters' => [
                'default' => [
                    'class' => Filesystem\Local\Adapter::class,
                    'config' => Filesystem\Local\Config::class,
                ],
            ],
            'id' => 'fs',
            'container' => true,
            'config' => [
                'class' => Filesystem\Config::class,
                'adapter' => 'default',
            ],
        ]);
        $bootstrap->bootstrap($app);

        $this->assertTrue($app->has('fs'));

        /** @var Filesystem\Filesystem $fs */
        $fs = $app->get('fs');
        $this->assertInstanceOf(Filesystem\Filesystem::class, $fs);
        $this->assertInstanceOf(Filesystem\Local\Adapter::class, $fs->getAdapter());

        $this->assertTrue(
            \Yii::$container->has(Filesystem\Filesystem::class)
        );


        \Yii::$app = null;
    }
}
