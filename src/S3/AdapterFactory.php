<?php

declare(strict_types=1);

namespace Wearesho\Yii\Filesystem\S3;

use Wearesho\Yii\Filesystem\AdapterFactoryInterface;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\FilesystemAdapter;
use Aws\S3\S3Client;
use yii\base;
use yii\base\InvalidConfigException;
use yii\di;

class AdapterFactory extends base\BaseObject implements AdapterFactoryInterface
{
    public ConfigInterface|string|array $config = EnvironmentConfig::class;

    public function create(): FilesystemAdapter
    {
        $config = $this->getConfig();
        $bucket = $config->getBucket();
        $prefix = $config->getPrefix();

        $arguments = [
            'endpoint' => $config->getEndpoint(),
            'version' => 'latest',
            'region' => $config->getRegion(),
        ];

        $client = new S3Client($arguments);
        return new AwsS3V3Adapter($client, $bucket, $prefix);
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
