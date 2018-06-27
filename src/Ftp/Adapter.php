<?php

namespace Wearesho\Yii\Filesystem\Ftp;

use yii\base;
use yii\di;
use League\Flysystem;
use Wearesho\Yii\Filesystem;

/**
 * Class Adapter
 * @package Wearesho\Yii\Filesystem\Ftp
 */
class Adapter extends Flysystem\Adapter\Ftp implements Filesystem\AdapterInterface, base\Configurable
{
    /** @var array|string|ConfigInterface reference */
    public $config = [
        'class' => EnvironmentConfig::class,
    ];

    /**
     * Adapter constructor.
     * @param array $config
     * @throws base\InvalidConfigException
     */
    public function __construct(array $config = [])
    {
        if (array_key_exists('config', $config)) {
            $this->config = $config['config'];
        }
        $this->config = di\Instance::ensure($this->config, ConfigInterface::class);

        $config += [
            'host' => $this->config->getHost(),
            'username' => $this->config->getUser(),
            'password' => $this->config->getPassword(),
            'port' => $this->config->getPort(),
            'root' => $this->config->getPrefix(),
        ];

        parent::__construct($config);
    }

    public function getBaseUrl(): string
    {
        return $this->config->getBaseUrl();
    }
}