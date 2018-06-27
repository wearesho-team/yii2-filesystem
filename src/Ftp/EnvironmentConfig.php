<?php

namespace Wearesho\Yii\Filesystem\Ftp;

use Horat1us\Environment;

/**
 * Class EnvironmentConfig
 * @package Wearesho\Yii\Filesystem\Ftp
 */
class EnvironmentConfig extends Environment\Yii2\Config implements ConfigInterface
{
    public $keyPrefix = 'FTP_';

    public function getHost(): string
    {
        return $this->getEnv('HOST');
    }

    public function getUser(): string
    {
        return $this->getEnv('USER');
    }

    public function getPassword(): string
    {
        // key and method name different because of backward-compatibility for SHO Art & Data
        return $this->getEnv('PASS', [$this, 'null']);
    }

    public function getPort(): int
    {
        return $this->getEnv('PORT', 21);
    }

    public function getPrefix(): string
    {
        // key and method name different because of backward-compatibility for SHO Art & Data
        return $this->getEnv('PATH', '');
    }

    public function getBaseUrl(): string
    {
        return $this->getEnv('BASE_URL');
    }
}
