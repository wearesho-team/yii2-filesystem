<?php

namespace Wearesho\Yii\Filesystem\Local;

/**
 * Interface ConfigInterface
 * @package Wearesho\Yii\Filesystem\Local
 */
interface ConfigInterface
{
    public function getSavePath(): string;

    public function getBaseUrl(): string;
}
