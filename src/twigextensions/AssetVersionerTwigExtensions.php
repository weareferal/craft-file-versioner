<?php

namespace weareferal\fileversioner\twigextensions;

use Craft;

use weareferal\fileversioner\FileVersioner;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;


class FileVersionerTwigExtensions extends AbstractExtension
{
    public function getName()
    {
        return 'FileVersioner';
    }

    public function getFilters()
    {
        return [
            new TwigFilter('version', [$this, 'getVersion']),
            new TwigFilter('v', [$this, 'getVersion'])
        ];
    }

    /**
     * Get a versioned file from a path
     * 
     */
    public function getVersion($path)
    {
        $plugin = FileVersioner::getInstance();
        if (!$plugin->settings->staticVersioningEnabled) {
            return $path;
        }
        try {
            $version = $plugin->keystore->get($path);
            if ($version) {
                return $version;
            }
        } catch (\Exception $e) {
            Craft::$app->getErrorHandler()->logException($e);
        }
        return $path;
    }
}
