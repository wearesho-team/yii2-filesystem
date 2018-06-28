<?php

namespace Wearesho\Yii\Filesystem\Tests;

use PHPUnit\Framework\TestCase;
use Wearesho\Yii\Filesystem;

/**
 * Class ConfigTest
 * @package Wearesho\Yii\Filesystem\Tests
 * @coversDefaultClass \Wearesho\Yii\Filesystem\Config
 */
class ConfigTest extends TestCase
{
    /** @var Filesystem\Config */
    protected $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = new Filesystem\Config();
    }

    public function testGetAdapter(): void
    {
        $this->assertEquals(
            's3',
            $this->config->getAdapter()
        );
        $this->config->adapter = 'default';
        $this->assertEquals(
            'default',
            $this->config->getAdapter()
        );
    }
}
