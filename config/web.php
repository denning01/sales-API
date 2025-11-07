<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [

        'request' => [
            'cookieValidationKey' => 'yourRandomSecretKeyHere',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],

        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
        ],

        // ✅ Disable session and authentication (for pure API)
       'user' => [
        'class' => 'yii\web\User',
        'enableSession' => false,
        'identityClass' => 'yii\web\IdentityInterface', // dummy
        'loginUrl' => null,
    ],

        'urlManager' => [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'enableStrictParsing' => false,
    'rules' => [
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['sale', 'offer', 'page'],
            'pluralize' => true,
        ],
        // Custom endpoints
        'POST sales/upload' => 'sale/upload',
        'POST sales/<id:\d+>/upload' => 'sale/upload',
        'GET pages/<slug:[a-zA-Z0-9_-]+>' => 'page/view-by-slug',
    ],


        ],

        'db' => $db,
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],

    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
