<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'class' => 'api\rest\ErrorHandler',
            'errorAction' => 'api/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName'  => false,
            'rules' => [
                '' => 'site',
                [
                    'class' => 'yii\rest\UrlRule',
                    'only' => ['view', 'create', 'options'],
                    'controller' => [
                        'setCheckout' => 'set-checkout',
                        'doCheckout' => 'do-checkout',
                    ],
                    'tokens' => ['{id}' => '<id:[A-Za-z0-9_.-]{32}>'],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'only' => ['create', 'options'],
                    'controller' => [
                        'doTransaction' => 'do-transaction',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];
