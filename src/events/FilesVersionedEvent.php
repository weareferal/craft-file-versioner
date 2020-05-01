<?php

namespace weareferal\fileversioner\events;

use yii\base\Event;

class FilesVersionedEvent extends Event
{
    public $versioned_paths = [];
}
