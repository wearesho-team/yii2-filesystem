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

    public function getKey(): ?string
    {
        return $this->getEnv('KEY', [$this, 'null',]);
    }

    public function getSecret(): ?string
    {
        return $this->getEnv('SECRET', [$this, 'null',]);
    }

    public function getVersion(): string
    {
        return $this->getEnv('VERSION', 'latest');
    }

    public function getRegion(): string
    {
        return $this->getEnv('REGION');
    }

    public function getBucket(): string
    {
        return $this->getEnv('BUCKET');
    }

    /**
     * @return string
     * @todo: implement default value using bucket, endpoint methods
     */
    public function getBaseUrl(): string
    {
        return $this->getEnv('BASE_URL');
    }

    public function getPrefix(): string
    {
        return $this->getEnv('PREFIX', '');
    }
}
