<?php

namespace weareferal\fileversioner\console\controllers;

use weareferal\fileversioner\FileVersioner;

use yii\console\Controller;
use yii\helpers\Console;
use yii\console\ExitCode;


/**
 * Static file versioning commands
 */
class StaticFilesController extends Controller
{
    /**
     * Scan and version all static files
     */
    public function actionScan()
    {
        $plugin = FileVersioner::getInstance();
        $settings = $plugin->getSettings();

        if (!$settings->staticVersioningEnabled) {
            $this->stdout('Static file versioning is not enabled. Please update your settings or "config/file-versioner.php" file.' . PHP_EOL, Console::FG_RED, Console::UNDERLINE);
            return ExitCode::CONFIG;
        }

        $this->stdout('Enabled Extensions:' . PHP_EOL, Console::FG_BLUE, Console::UNDERLINE);
        foreach (explode(',', $settings->staticVersioningExtensions) as $extension) {
            $this->stdout($extension . PHP_EOL);
        }
        $this->stdout(PHP_EOL);

        $result = $plugin->staticversioner->scan();

        $this->stdout('Found Paths:' . PHP_EOL, Console::FG_BLUE, Console::UNDERLINE);
        foreach ($result["paths"] as $path) {
            $this->stdout($path . PHP_EOL);
        }
        $this->stdout(PHP_EOL);

        if (array_key_exists("deleted_paths", $result)) {
            $this->stdout('Deleted Paths:' . PHP_EOL, Console::FG_RED, Console::UNDERLINE);
            foreach ($result["deleted_paths"] as $path) {
                $this->stdout($path . PHP_EOL);
            }
            $this->stdout(PHP_EOL);
        }

        $this->stdout('Generated Paths:' . PHP_EOL, Console::FG_GREEN, Console::UNDERLINE);
        foreach ($result["versioned_paths"] as $path) {
            $this->stdout($path . PHP_EOL);
        }
    }
}
