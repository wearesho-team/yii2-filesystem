<?php

declare(strict_types=1);

namespace Wearesho\Yii\Filesystem\Local;

use League\Flysystem\UrlGeneration\TemporaryUrlGenerator;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use League\Flysystem\UrlGeneration\PublicUrlGenerator;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\MimeTypeDetection\MimeTypeDetector;
use League\Flysystem\Config;
use yii\web\UrlManager;

class Adapter extends LocalFilesystemAdapter implements TemporaryUrlGenerator, PublicUrlGenerator
{
    private UrlManager $urlManager;

    public function __construct(
        string               $location,
        ?VisibilityConverter $visibility = null,
        int                  $writeFlags = LOCK_EX,
        int                  $linkHandling = self::DISALLOW_LINKS,
        ?MimeTypeDetector    $mimeTypeDetector = null,
        bool                 $lazyRootCreation = false,
        bool                 $useInconclusiveMimeTypeFallback = false,
        ?UrlManager          $urlManager = null
    ) {
        parent::__construct(
            $location,
            $visibility,
            $writeFlags,
            $linkHandling,
            $mimeTypeDetector,
            $lazyRootCreation,
            $useInconclusiveMimeTypeFallback
        );
        $this->urlManager = is_null($urlManager) ? \Yii::$app->urlManager : $urlManager;
    }

    public function publicUrl(string $path, Config $config): string
    {
        return $this->urlManager->createAbsoluteUrl('') . $path;
    }

    public function temporaryUrl(string $path, \DateTimeInterface $expiresAt, Config $config): string
    {
        return $this->urlManager->createAbsoluteUrl('') . $path;
    }
}
