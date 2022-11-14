<?php

namespace Wearesho\Yii\Filesystem\Replica;

use yii\base;
use yii\di;
use League\Flysystem;
use Wearesho\Yii\Filesystem;

/**
 * Class Adapter
 * @package Wearesho\Yii\Filesystem
 * @link https://github.com/thephpleague/flysystem-replicate-adapter/blob/1.0.1/src/ReplicateAdapter.phpss
 */
class Adapter extends base\BaseObject implements Filesystem\AdapterInterface
{
    /** @var string|array|Filesystem\AdapterInterface definition */
    public $master;

    /** @var string[]|array[]|Filesystem\AdapterInterface[] definitions */
    public $slaves = [];

    /**
     * @throws base\InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->master = di\Instance::ensure($this->master, Filesystem\AdapterInterface::class);

        if (!is_array($this->slaves)) {
            throw new base\InvalidConfigException("Replicas definition have to be an array");
        }
        $this->slaves = array_map(function ($reference): Filesystem\AdapterInterface {
            /** @var Filesystem\AdapterInterface $replica */
            $replica = di\Instance::ensure($reference, Filesystem\AdapterInterface::class);
            return $replica;
        }, $this->slaves);
    }


    /**
     * @inheritdoc
     */
    public function write($path, $contents, Flysystem\Config $config)
    {
        return $this->reduce(function (Flysystem\AdapterInterface $adapter) use ($path, $contents, $config) {
            return $adapter->write($path, $contents, $config);
        });
    }

    /**
     * @inheritdoc
     */
    public function writeStream($path, $resource, Flysystem\Config $config)
    {
        return $this->reduce(function (Filesystem\AdapterInterface $adapter) use ($path, &$resource, $config) {
            if (!$resource = $this->ensureSeekable($resource, $path)) {
                return false;
            }
            return $adapter->writeStream($path, $resource, $config);
        });
    }

    /**
     * @inheritdoc
     */
    public function update($path, $contents, Flysystem\Config $config)
    {
        return $this->reduce(function (Filesystem\AdapterInterface $adapter) use ($path, $contents, $config) {
            return $adapter->has($path)
                ? $adapter->update($path, $contents, $config)
                : $adapter->write($path, $contents, $config);
        });
    }

    /**
     * @inheritdoc
     */
    public function updateStream($path, $resource, Flysystem\Config $config)
    {

        return $this->reduce(function (Filesystem\AdapterInterface $adapter) use ($path, &$resource, $config) {
            if (!$resource = $this->ensureSeekable($resource, $path)) {
                return false;
            }
            return $adapter->has($path)
                ? $adapter->updateStream($path, $resource, $config)
                : $adapter->writeStream($path, $resource, $config);
        });
    }

    /**
     * @inheritdoc
     */
    public function rename($path, $newPath)
    {
        return $this->reduce(function (Filesystem\AdapterInterface $adapter) use ($path, $newPath) {
            return $adapter->rename($path, $newPath);
        });
    }

    /**
     * @inheritdoc
     */
    public function copy($path, $newPath)
    {
        return $this->reduce(function (Filesystem\AdapterInterface $adapter) use ($path, $newPath) {
            return $adapter->copy($path, $newPath);
        });
    }

    /**
     * @inheritdoc
     */
    public function delete($path)
    {
        if (!$this->master->delete($path)) {
            return false;
        }

        return $this->reduce(function (Filesystem\AdapterInterface $adapter) use ($path) {
            if (!$adapter->has($path)) {
                return true;
            }
            return $adapter->delete($path);
        }, false);
    }

    /**
     * @inheritdoc
     */
    public function setVisibility($path, $visibility)
    {
        return $this->reduce(function (Filesystem\AdapterInterface $adapter) use ($path, $visibility) {
            return $adapter->setVisibility($path, $visibility);
        });
    }

    /**
     * @inheritdoc
     */
    public function deleteDir($dirName)
    {
        return $this->reduce(function (Filesystem\AdapterInterface $adapter) use ($dirName) {
            return $adapter->deleteDir($dirName);
        });
    }

    /**
     * @inheritdoc
     */
    public function createDir($dirName, Flysystem\Config $config)
    {
        return $this->reduce(function (Filesystem\AdapterInterface $adapter) use ($dirName, $config) {
            return $adapter->createDir($dirName, $config);
        });
    }

    /**
     * @inheritdoc
     */
    public function has($path)
    {
        return $this->master->has($path);
    }

    /**
     * @inheritdoc
     */
    public function read($path)
    {
        return $this->master->read($path);
    }

    /**
     * @inheritdoc
     */
    public function readStream($path)
    {
        return $this->master->readStream($path);
    }

    /**
     * @inheritdoc
     */
    public function listContents($directory = '', $recursive = false)
    {
        return $this->master->listContents($directory, $recursive);
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($path)
    {
        return $this->master->getMetadata($path);
    }

    /**
     * @inheritdoc
     */
    public function getSize($path)
    {
        return $this->master->getSize($path);
    }

    /**
     * @inheritdoc
     */
    public function getMimetype($path)
    {
        return $this->master->getMimetype($path);
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp($path)
    {
        return $this->master->getTimestamp($path);
    }

    /**
     * @inheritdoc
     */
    public function getVisibility($path)
    {
        return $this->master->getVisibility($path);
    }

    public function getBaseUrl(): string
    {
        return $this->master->getBaseUrl();
    }

    protected function reduce(\Closure $closure, bool $withMaster = true)
    {
        $result = null;

        foreach ($this->adapters($withMaster) as $adapter) {
            $result = $closure($adapter);
            if ($result === false) {
                return $result;
            }
        }

        return $result;
    }

    /**
     * @param bool $withMaster
     * @return Filesystem\AdapterInterface[]
     */
    protected function adapters(bool $withMaster = true): array
    {
        if (!$withMaster) {
            return $this->slaves;
        }

        return array_merge([$this->master], $this->slaves);
    }

    /**
     * Rewinds the stream, or returns the source stream if not seekable.
     *
     * @param resource $resource The resource to rewind.
     * @param string $path The path where the resource exists.
     *
     * @return resource|false A stream set to position zero.
     */
    protected function ensureSeekable($resource, $path)
    {
        if ($resource && Flysystem\Util::isSeekableStream($resource) && rewind($resource)) {
            return $resource;
        }
        $stream = $this->master->readStream($path);

        // ensure safety. disabled tests was broken without this code
        if (is_resource($stream)) {
            return $stream;
        }
        if (is_array($stream) && array_key_exists('stream', $stream)) {
            return $stream['stream'];
        }
        return false;
    }
}
