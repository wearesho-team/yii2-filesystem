<?php

namespace Wearesho\Yii\Filesystem;

use Aws\S3\S3Client;

use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Adapter\Local as LocalAdapter;

/**
 * Class FilesystemFactory
 * @package common\factories
 */
class FilesystemFactory
{
    public static function instantiate()
    {
        if ($endpoint = getenv('S3_ENDPOINT')) {
            $client = new S3Client([
                'endpoint' => $endpoint,
                'credentials' => [
                    'key' => getenv("S3_KEY"),
                    'secret' => getenv("S3_SECRET"),
                ],
                'version' => getenv("S3_VERSION"),
                'region' => getenv("S3_REGION"),
            ]);
            $adapter = new AwsS3Adapter($client, getenv("S3_BUCKET"));
            $baseUrl = getenv('S3_BASE_URL');
        } else {
            $adapter = new LocalAdapter(\Yii::getAlias('@fileStorage'));
            $baseUrl = \Yii::$app->urlManager->createUrl('/');
        }

        return new Filesystem($adapter, $baseUrl);
    }
}