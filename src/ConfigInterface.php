<?php

namespace Wearesho\Yii\Filesystem;

/**
 * Interface ConfigInterface
 * @package Wearesho\Yii\Filesystem
 */
interface ConfigInterface
{
    public function getAdapter(): string;
}
