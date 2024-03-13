<?php

return [
    'id' => 'test',
    'language' => 'de-CH',
    'basePath' => dirname(__DIR__, 2),
    'vendorPath' => dirname(__DIR__, 3) . '/vendor',
    'controllerNamespace' => 'sandritsch91\yii2\formwizard\tests\Support\Data\controllers',
    'viewPath' => '@app/Support/Data/views',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'assetManager' => [
            'baseUrl' => 'http://127.0.0.1',
            'basePath' => dirname(__DIR__, 2) . '/_output',
        ],
        'request' => [
            'cookieValidationKey' => 'FeVWXG3y1c0Q1JGdGbQacuQt6ZBHLk3W',
            'enableCsrfValidation' => false
        ],
        'urlManager' => [
            'enablePrettyUrl' => false,
            'showScriptName' => true
        ],
    ]
];
