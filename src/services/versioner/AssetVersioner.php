<?php

/**
 * Asset Versioner plugin for Craft CMS 3.x
 *
 * Automatically create cache-busting versions of all your assets
 *
 * @link      https://weareferal.com
 * @copyright Copyright (c) 2020 Timmy O'Mahony
 */

namespace weareferal\fileversioner\services\versioner;


use weareferal\fileversioner\FileVersioner;
use weareferal\fileversioner\services\versioner\BaseVersioner;

use Craft;
use craft\elements\Asset;


/**
 * Asset Versioner
 * 
 * A class to handle the scanning and versioning of asset files, usually 
 * uploaded by the user.
 *
 * @author    Timmy O'Mahony
 * @package   FileVersioner
 * @since     1.1.0
 */
class AssetVersioner extends BaseVersioner
{
    /**
     * Before asset is saved
     * 
     * 
     */
    public function onBeforeAssetSaved($event)
    {

        $asset = $event->sender;
        $plugin = FileVersioner::getInstance();
        $settings = $plugin->getSettings();
        $extensions = explode(",", $settings->assetVersioningExtensions);

        $pathinfo = pathinfo($asset->filename);
        $filename = $pathinfo['filename'];
        $extension = $pathinfo['extension'];

        if (!in_array($extension, $extensions)) {
            Craft::warning($filename . ' cannot be versioned (invalid extension)', 'file-versioner');
            return;
        }

        Craft::debug($filename . PHP_EOL, 'file-versioner');
        if (preg_match("/^.+\.\w{32}$/", $filename)) {
            Craft::warning($filename . ' is already versioned', 'file-versioner');
            return;
        }

        // We're saving a newly uploaded asset
        if ($event->isNew) {
            $file = file_get_contents($asset->tempFilePath);

            // If saving a new file, we need to update the `newLocation` field
            // for the file to be saved correctly. This field takes the format
            // "{folder:<id>}<filename>" where id is the folder id. Unfortunately
            // by the time this event handler is run, the folder id has been
            // deleted so we have to extract it again
            $_pathinfo = pathinfo($asset->newLocation);
            $asset->newLocation = $_pathinfo['filename'] . '.' . md5($file) . '.' . $_pathinfo['extension'];
        } else {
            $file = stream_get_contents($asset->stream);
            $asset->newLocation = "{folder:{$asset->folderId}}" . $filename . '.' . md5($file) . '.' . $extension;
        }
    }

    /**
     * Scan asset files
     * 
     * Scan all volumes for asset files and version if they match extension
     */
    public function scan($dry_run = false)
    {
        $plugin = FileVersioner::getInstance();
        $settings = $plugin->getSettings();
        $extensions = explode(",", $settings->assetVersioningExtensions);
        $result = [
            'versioned_assets' => [],
            'skipped_assets' => [],
            'failed_assets' => [],
            'assets' => []
        ];

        $publicVolumeIds = Craft::$app->volumes->getPublicVolumeIds();
        $assets = Asset::find()->volumeId($publicVolumeIds);
        foreach ($assets as $asset) {
            $pathinfo = pathinfo($asset->filename);
            array_push($result['assets'], $pathinfo['basename']);
            if (in_array($pathinfo['extension'], $extensions) && !preg_match("/^.+\.\w{32}$/", $pathinfo['filename'])) {
                // Trigger a re-save which will automatically run the above
                // onBeforeAssetSaved handler
                if (!$dry_run) {
                    Craft::$app->elements->saveElement($asset);
                }
                array_push($result['versioned_assets'], $pathinfo['basename']);
            } else {
                array_push($result['skipped_assets'], $pathinfo['basename']);
            }
        }
        return $result;
    }
}
