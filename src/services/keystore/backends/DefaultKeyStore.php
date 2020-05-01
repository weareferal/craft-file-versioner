<?php

namespace weareferal\fileversioner\services\backends;

use Craft;

use weareferal\fileversioner\services\keystore\KeyStoreInterface;
use weareferal\fileversioner\services\keystore\KeyStore;

class DefaultKeyStore extends KeyStore implements KeyStoreInterface
{

    public function get($key)
    {
        $cache = Craft::$app->cache->get("craft_assert_versioner");
        return $cache[$key];
    }

    public function update($versioned_paths)
    {
        Craft::$app->cache->set("craft_assert_versioner", $versioned_paths, 0);
    }
}
