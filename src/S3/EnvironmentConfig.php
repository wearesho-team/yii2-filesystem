<?php

namespace Wearesho\Yii\Filesystem\S3;

use Horat1us\Environment;

/**
 * Class EnvironmentConfig
 * @package Wearesho\Yii\Filesystem
 */
class EnvironmentConfig extends Environment\Yii2\Config implements ConfigInterface
{
    public $keyPrefix = 'S3_';

    public function getEndpoint(): string
    {
        return $this->getEnv('ENDPOINT');
    }

    public function getRegion(): ?string
    {
        return $this->getEnv('REGION', [$this, 'null',]);
    }

    public function getBucket(): string
    {
        return $this->getEnv('BUCKET');
    }

    public function getPrefix(): string
    {
        return $this->getEnv('PREFIX', '');
    }
}
