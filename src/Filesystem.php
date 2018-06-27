<?php

namespace Wearesho\Yii\Filesystem;

use League\Flysystem;

/**
 * Class Filesystem
 * @package Wearesho\Yii\Filesystem
 *
 * @property-read AdapterInterface $adapter
 */
class Filesystem extends Flysystem\Filesystem
{
    public function __construct(AdapterInterface $adapter, $config = null)
    {
        parent::__construct($adapter, $config);
    }

    public function getUrl(string $path): string
    {
        return rtrim($this->adapter->getBaseUrl(), '/') . '/' . ltrim($path, '/');
    }
}
