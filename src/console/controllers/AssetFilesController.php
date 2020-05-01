<?php

namespace weareferal\fileversioner\console\controllers;

use weareferal\fileversioner\FileVersioner;

use yii\console\Controller;
use yii\helpers\Console;
use yii\console\ExitCode;


/**
 * Asset file versioning commands
 */
class AssetFilesController extends Controller
{
    /**
     * Scan and version all existing asset files
     */
    public function actionScan()
    {
        $plugin = FileVersioner::getInstance();
        $settings = $plugin->getSettings();

        if (!$settings->assetVersioningEnabled) {
            $this->stdout('Asset file versioning is not enabled. Please update your settings or "config/file-versioner.php" file.' . PHP_EOL, Console::FG_RED, Console::UNDERLINE);
            return ExitCode::CONFIG;
        }

        $this->stdout('Enabled Extensions:' . PHP_EOL, Console::FG_BLUE, Console::UNDERLINE);
        foreach (explode(',', $settings->assetVersioningExtensions) as $extension) {
            $this->stdout($extension . PHP_EOL);
        }
        $this->stdout(PHP_EOL);

        $result = $plugin->assetversioner->scan();

        $this->stdout('Found Assets:' . PHP_EOL, Console::FG_BLUE, Console::UNDERLINE);
        foreach ($result["assets"] as $path) {
            $this->stdout($path . PHP_EOL);
        }
        $this->stdout(PHP_EOL);

        if (array_key_exists("skipped_assets", $result)) {
            $this->stdout('Skipped Assets:' . PHP_EOL, Console::FG_YELLOW, Console::UNDERLINE);
            foreach ($result["skipped_assets"] as $path) {
                $this->stdout($path . PHP_EOL);
            }
            $this->stdout(PHP_EOL);
        }

        if (array_key_exists("failed_assets", $result)) {
            $this->stdout('Failed Assets:' . PHP_EOL, Console::FG_RED, Console::UNDERLINE);
            foreach ($result["failed_assets"] as $path) {
                $this->stdout($path . PHP_EOL);
            }
            $this->stdout(PHP_EOL);
        }

        $this->stdout('Versioned Assets:' . PHP_EOL, Console::FG_GREEN, Console::UNDERLINE);
        foreach ($result["versioned_assets"] as $path) {
            $this->stdout($path . PHP_EOL);
        }

        $this->stdout(PHP_EOL);
    }
}
