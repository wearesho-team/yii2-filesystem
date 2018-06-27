<?php

namespace Wearesho\Yii\Filesystem\Local;

use Horat1us\Environment;

/**
 * Class EnvironmentConfig
 * @package Wearesho\Yii\Filesystem\Local
 */
class EnvironmentConfig extends Environment\Yii2\Config implements ConfigInterface
{
    public $keyPrefix = 'FILESYSTEM_LOCAL_';

    public function getSavePath(): string
    {
        return $this->getEnv('SAVE_PATH');
    }

    public function getBaseUrl(): string
    {
        return $this->getEnv('BASE_URL');
    }
}
