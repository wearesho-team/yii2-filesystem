<?php

declare(strict_types=1);

namespace Wearesho\Yii\Filesystem\Local;

use Wearesho\Yii\Filesystem\AdapterFactoryInterface;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\FilesystemAdapter;
use yii\base\InvalidConfigException;
use yii\base;
use yii\di;

class AdapterFactory extends base\BaseObject implements AdapterFactoryInterface
{
    public ConfigInterface|array|string $config = EnvironmentConfig::class;

    public function create(): FilesystemAdapter
    {
        return new LocalFilesystemAdapter($this->getConfig()->getSavePath());
    }

    /**
     * @throws InvalidConfigException
     */
    private function getConfig(): ConfigInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return di\Instance::ensure($this->config, ConfigInterface::class);
    }
}
