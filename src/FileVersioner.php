<?php

namespace weareferal\fileversioner;

use weareferal\fileversioner\services\versioner\StaticVersioner;
use weareferal\fileversioner\services\versioner\AssetVersioner;
use weareferal\fileversioner\services\keystore\KeyStore;

use weareferal\fileversioner\models\Settings;
use weareferal\fileversioner\twigextensions\FileVersionerTwigExtensions;
use weareferal\fileversioner\events\FilesVersionedEvent;

use Craft;
use craft\base\Plugin;
use craft\events\ModelEvent;
use craft\console\Application as ConsoleApplication;
use craft\elements\Asset;

use yii\base\Event;


class FileVersioner extends Plugin
{
    public static $plugin;
    public $schemaVersion = '1.0.0';

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'weareferal\fileversioner\console\controllers';
        }

        $this->setComponents([
            'staticversioner' => StaticVersioner::class,
            'assetversioner' => AssetVersioner::class,
            'keystore' => KeyStore::create()
        ]);

        Craft::$app->view->registerTwigExtension(new FileVersionerTwigExtensions());

        // When a static scan takes place, update the keystore with the new values
        Event::on(
            StaticVersioner::class,
            StaticVersioner::EVENT_AFTER_FILES_VERSIONED,
            function (FilesVersionedEvent $event) {
                $this->keystore->update($event->versioned_paths);
            }
        );

        // Version newly saved assets
        Event::on(
            Asset::class,
            Asset::EVENT_BEFORE_SAVE,
            function (ModelEvent $event) {
                if ($this->settings->assetVersioningEnabled) {
                    $this->assetversioner->onBeforeAssetSaved($event);
                }
            }
        );
    }

    protected function createSettingsModel()
    {
        return new Settings();
    }

    protected function settingsHtml(): string
    {
        $view = Craft::$app->getView();
        return $view->renderTemplate(
            'file-versioner/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
