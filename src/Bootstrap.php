<?php

declare(strict_types=1);

namespace Wearesho\Yii\Filesystem;

use yii\base;
use yii\di;
use League\Flysystem;

class Bootstrap extends base\BaseObject implements base\BootstrapInterface
{
    /** @var array[]|string[]|AdapterFactoryInterface[] array of definitions */
    public array $adapters = [
        'local' => [
            'class' => Local\AdapterFactory::class,
            'config' => [
                'class' => Local\EnvironmentConfig::class,
                'keyPrefix' => 'FILESYSTEM_LOCAL_',
            ],
        ],
        's3' => [
            'class' => S3\AdapterFactory::class,
            'config' => [
                'class' => S3\EnvironmentConfig::class,
                'keyPrefix' => 'S3_',
            ],
        ],
    ];

    public array|string|ConfigInterface $config = [
        'class' => EnvironmentConfig::class,
        'keyPrefix' => 'FILESYSTEM_',
    ];

    /** @var bool Should container be configured in bootstrap */
    public bool $container = false;

    /** @var string|null \Yii::$app component name to be set. If null, no component will be configured */
    public ?string $id = null;

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
            $app->set($this->id, $this->getFilesystem(...));
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
            Flysystem\Filesystem::class,
            $this->getFilesystem(...)
        );
    }

    public function getFilesystem(): Flysystem\Filesystem
    {
        return new Flysystem\Filesystem(
            adapter: $this->getAdapterFactory()->create(),
        );
    }

    /**
     * @throws base\InvalidConfigException
     */
    protected function getAdapterFactory(): AdapterFactoryInterface
    {
        $adapterKey = $this->config->getAdapter();
        if (!array_key_exists($adapterKey, $this->adapters)) {
            throw new base\InvalidConfigException("Adapter {$adapterKey} is not configured");
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return di\Instance::ensure(
            $this->adapters[$adapterKey],
            AdapterFactoryInterface::class
        );
    }
}
