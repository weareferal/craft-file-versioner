<?php

namespace weareferal\fileversioner\services\versioner;

use craft\base\Component;

abstract class BaseVersioner extends Component
{
    const EVENT_AFTER_FILES_VERSIONED = 'afterFilesVersioned';
}
