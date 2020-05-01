<?php

namespace weareferal\fileversioner\models;

use craft\base\Model;


class Settings extends Model
{
    // Enable automatic versioning of static files
    public $staticVersioningEnabled = false;

    // The folder within @webroot to store versioned files
    public $staticVersioningPrefix = 'versions';

    // The file extensions to target for versioning
    public $staticVersioningExtensions = "css,js";

    // Enable automatic versioning of new static files
    public $assetVersioningEnabled = false;

    // The file extensions to target for versioning
    public $assetVersioningExtensions = "png,jpg,jpeg,gif";

    public function rules()
    {
        return [
            [
                [
                    'staticVersioningEnabled',
                    'assetVersioningEnabled'
                ],
                'boolean'
            ],
            [
                [
                    'staticVersioningExtensions',
                    'staticVersioningPrefix',
                    'assetVersioningExtensions',
                ],
                'string',
            ],
            [
                [
                    'staticVersioningExtensions',
                    'assetVersioningExtensions',
                ],
                'required',
            ],
            [
                [
                    'staticVersioningExtensions',
                    'assetVersioningExtensions',
                ],
                'match',
                'pattern' => '/^(?:\w{2,10})(?:,\w{2,10})*$/',
                'message' => 'Please enter a comma-separated list of extensions'
            ]
        ];
    }
}
