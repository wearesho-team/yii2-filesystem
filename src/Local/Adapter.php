<?php

namespace Wearesho\Yii\Filesystem\Local;

use yii\base;
use yii\di;
use Wearesho\Yii\Filesystem;
use League\Flysystem;

/**
 * Class Adapter
 * @package Wearesho\Yii\Filesystem\Local
 */
class Adapter extends Flysystem\Adapter\Local implements Filesystem\AdapterInterface, base\Configurable
{
    /** @var array|string|ConfigInterface definition */
    public $config = [
        'class' => EnvironmentConfig::class,
    ];

    /**
     * Adapter constructor.
     * @param array $config
     * @throws base\InvalidConfigException
     */
    public function __construct(
        array $config = []
    ) {
        \Yii::configure($this, $config);
        $this->config = di\Instance::ensure($this->config, ConfigInterface::class);

        $root = $this->config->getSavePath();

        parent::__construct(
            $root,
            $this->writeFlags,
            self::DISALLOW_LINKS,
            $this->permissionMap ?? []
        );
    }

    public function getBaseUrl(): string
    {
        return $this->config->getBaseUrl();
    }
}
