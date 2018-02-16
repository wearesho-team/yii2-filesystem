<?php

namespace Wearesho\Yii\Filesystem;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem as Flysystem;

/**
 * Class Filesystem
 * @package common\components
 */
class Filesystem extends Flysystem
{
    /** @var string */
    protected $baseUrl;

    public function __construct(AdapterInterface $adapter, string $baseUrl, $config = null)
    {
        parent::__construct($adapter, $config);
        $this->baseUrl = $baseUrl;
    }

    public function getUrl(string $path): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
    }
}
