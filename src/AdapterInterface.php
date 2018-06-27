<?php

namespace Wearesho\Yii\Filesystem;

use League\Flysystem;

/**
 * Interface Adapter
 * @package Wearesho\Yii\Filesystem
 */
interface AdapterInterface extends Flysystem\AdapterInterface
{
    public function getBaseUrl(): string;
}
