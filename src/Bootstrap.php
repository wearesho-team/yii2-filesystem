<?php

namespace Wearesho\Yii\Filesystem;

use yii\base;
use yii\di;

/**
 * Class Bootstrap
 * @package Wearesho\Yii\Filesystem
 */
class Bootstrap extends base\BaseObject implements base\BootstrapInterface
{
    /** @var array[]|string[]|AdapterInterface[] array of definitions */
    public $adapters = [
        'local' => [
            'class' => Local\Adapter::class,
            'config' => [
                'class' => Local\EnvironmentConfig::class,
                'keyPrefix' => 'FILESYSTEM_LOCAL_',
            ],
        ],
        's3' => [
            'class' => S3\Adapter::class,
            'config' => [
                'class' => S3\Adapter::class,
                'config' => [
                    'class' => S3\EnvironmentConfig::class,
                    'keyPrefix' => 'S3_',
                ],
            ],
        ],
        'ftp' => [
            'class' => Ftp\Adapter::class,
            'config' => [
                'class' => Ftp\EnvironmentConfig::class,
                'keyPrefix' => 'FTP_',
            ],
        ],
    ];

    /** @var array|string|ConfigInterface definition */
    public $config = [
        'class' => EnvironmentConfig::class,
        'keyPrefix' => 'FILESYSTEM_',
    ];

    /** @var bool Should container be configured in bootstrap */
    public $container = false;

    /** @var string \Yii::$app component name to be set. If null, no component will be configured */
    public $id = null;

    /**
     * @throws base\InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->config = di\Instance::ensure($this->config, ConfigInterface::class);
    }

    /**
     * @inheritdoc
     * @throws base\InvalidConfigException
     */
    public function bootstrap($app): void
    {
        if (!is_null($this->id)) {
            /** @noinspection MissedFieldInspection */
            $app->set($this->id, [
                'class' => Filesystem::class,
                'adapter' => $this->getAdapterReference(),
            ]);
        }

        if ($this->container === true) {
            $this->configure(\Yii::$container);
        }
    }

    /**
     * @param di\Container $container
     * @throws base\InvalidConfigException
     */
    public function configure(di\Container $container): void
    {
        $container->setSingleton(
            Filesystem::class,
            [
                'class' => Filesystem::class,
                'adapter' => $this->getAdapterReference(),
            ]
        );
    }

    /**
     * @return array|string
     * @throws base\InvalidConfigException
     */
    public function getAdapterReference()
    {
        $adapterKey = $this->config->getAdapter();
        if (!array_key_exists($adapterKey, $this->adapters)) {
            throw new base\InvalidConfigException("Adapter {$adapterKey} is not configured");
        }

        return $this->adapters[$adapterKey];
    }
}
