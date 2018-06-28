<?php

namespace Wearesho\Yii\Filesystem;

use yii\base;
use yii\di;
use League\Flysystem;

/**
 * Class Filesystem
 * @package Wearesho\Yii\Filesystem
 *
 * @property AdapterInterface $adapter
 */
class Filesystem extends Flysystem\Filesystem implements base\Configurable
{
    /**
     * Filesystem constructor.
     * @param array $config
     * @throws base\InvalidConfigException
     */
    public function __construct(array $config = [])
    {
        if (!array_key_exists('adapter', $config)) {
            throw new base\InvalidConfigException("Adapter have to specified in config");
        }

        /** @var AdapterInterface $adapter */
        $adapter = di\Instance::ensure($config['adapter'], AdapterInterface::class);

        parent::__construct($adapter, $config['config'] ?? null);
    }

    public function getUrl(string $path): string
    {
        return rtrim($this->adapter->getBaseUrl(), '/') . '/' . ltrim($path, '/');
    }
}
