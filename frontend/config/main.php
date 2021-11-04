<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
//    'homeUrl' => '/',
    'components' => [
        'request' => [
//            'baseUrl' => '',
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
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
//        'errorHandler' => [
//            'errorAction' => 'site/error',
//        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/cargo'],
//                    'ruleConfig' => [
//                        'class' => 'yii\rest\UrlRule',
//                        'defaults' => [
//                            'expand' => '', //设置需要额外放入的字段， 如关联表的字段
//                        ]
//                    ],
                    'extraPatterns' => [
                        'POST create' => 'create',//http动词 资源名 动作名
                        'POST upload' => 'upload' //http动词 资源名 动作名
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/user']
                ],
            ],
        ],

    ],
    'modules' => [
        'v1' => [
            'basePath' => 'frontend/modules/v1',
            'class' => 'frontend\modules\v1\Module',
        ],
    ],
    'params' => $params,
];
