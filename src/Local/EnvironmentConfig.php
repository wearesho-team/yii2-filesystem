<?php

declare(strict_types=1);

namespace Wearesho\Yii\Filesystem\Local;

use Horat1us\Environment;

class EnvironmentConfig extends Environment\Yii2\Config implements ConfigInterface
{
    public $keyPrefix = 'FILESYSTEM_LOCAL_';

    public function getSavePath(): string
    {
        return $this->getEnv('SAVE_PATH');
    }
}
