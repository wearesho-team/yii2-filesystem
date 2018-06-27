<?php

namespace Wearesho\Yii\Filesystem\Ftp;

/**
 * Interface ConfigInterface
 * @package Wearesho\Yii\Filesystem\Ftp
 */
interface ConfigInterface
{
    public function getHost(): string;

    public function getUser(): string;

    public function getPassword(): ?string;

    public function getPort(): int;

    public function getPrefix(): string;

    public function getBaseUrl(): string;
}
