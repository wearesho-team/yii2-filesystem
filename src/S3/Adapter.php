<?php

namespace Wearesho\Yii\Filesystem\S3;

use yii\base;
use yii\di;
use Aws\S3\S3Client;
use League\Flysystem;
use Wearesho\Yii\Filesystem;

/**
 * Class Adapter
 * @package Wearesho\Yii\Filesystem\S3
 */
class Adapter extends Flysystem\AwsS3v3\AwsS3Adapter implements Filesystem\AdapterInterface, base\Configurable
{
    /** @var array|string|ConfigInterface definition */
    public $config = [
        'class' => EnvironmentConfig::class,
    ];

    /**
     * Adapter constructor.
     * @param array $config
     * @throws base\InvalidConfigException
     */
    public function __construct(
        array $config = []
    ) {
        \Yii::configure($this, $config);
        $this->config = di\Instance::ensure($this->config, ConfigInterface::class);

        $bucket = $this->config->getBucket();
        $prefix = $this->config->getPrefix();

        $key = $this->config->getKey();
        $secret = $this->config->getSecret();
        $credentials = is_null($key) || is_null($secret)
            ? []
            : compact('key', 'secret');

        $client = new S3Client([
            'endpoint' => $this->config->getEndpoint(),
            'credentials' => $credentials,
            'version' => $this->config->getVersion(),
            'region' => $this->config->getRegion(),
        ]);

        parent::__construct($client, $bucket, $prefix, $this->options);
    }

    public function getBaseUrl(): string
    {
        return $this->config->getBaseUrl();
    }
}
