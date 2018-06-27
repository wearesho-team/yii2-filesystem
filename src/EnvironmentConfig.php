<?php

namespace Wearesho\Yii\Filesystem;

use Horat1us\Environment;

/**
 * Class EnvironmentConfig
 * @package Wearesho\Yii\Filesystem
 */
class EnvironmentConfig extends Environment\Yii2\Config implements ConfigInterface
{
    public $keyPrefix = 'FILESYSTEM_';

    public function getAdapter(): string
    {
        return $this->getEnv('ADAPTER', 'local');
    }
}
