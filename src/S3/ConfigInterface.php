<?php

namespace Wearesho\Yii\Filesystem\S3;

/**
 * Interface ConfigInterface
 * @package Wearesho\Yii\Filesystem
 */
interface ConfigInterface
{
    public function getEndpoint(): string;

    public function getRegion(): ?string;

    public function getBucket(): string;

    public function getPrefix(): string;
}
