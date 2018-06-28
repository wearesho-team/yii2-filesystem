<?php

namespace Wearesho\Yii\Filesystem\Tests\Local;

use PHPUnit\Framework\TestCase;
use Wearesho\Yii\Filesystem;

/**
 * Class ConfigTest
 * @package Wearesho\Yii\Filesystem\Tests\Local
 */
class ConfigTest extends TestCase
{
    /** @var Filesystem\Local\Config */
    protected $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = new Filesystem\Local\Config();
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessage basePath is not configured
     */
    public function testEmptyBaseUrl(): void
    {
        $this->config->getBaseUrl();
    }

    public function testBaseUrl(): void
    {
        $this->config->baseUrl = 'https://wearesho.com';
        $this->assertEquals('https://wearesho.com', $this->config->getBaseUrl());
    }

    public function testSavePath(): void
    {
        $this->assertEquals(
            \Yii::getAlias('@runtime'),
            $this->config->getSavePath()
        );

        $this->config->savePath = '/root';
        $this->assertEquals('/root', $this->config->getSavePath());
    }
}
