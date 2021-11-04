<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'timeZone' => 'Asia/Shanghai',
//    'homeUrl' => '/admin',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
//            'csrfCookie' => [
//                'httpOnly' => true,
//                'path' => '/',
//            ],
//            'baseUrl' => '/admin'
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=huafu',
//            'dsn' => 'mysql:host=139.9.249.149;dbname=huafu',
            'username' => 'root',
//            'password' => 'cuitloopMysql123',
            'password' => 'loopdzg123',
            'charset' => 'utf8',
        ],
        'user' => [
            'identityClass' => 'common\models\Users', //允许认证的表
            'enableAutoLogin' => true,
            'enableSession' => false,
//            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
//        'session' => [
//             this is the name of the session cookie used for login on the backend
//            'name' => 'advanced-backend',
//        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
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
       /* 'errorHandler' => [
            'errorAction' => 'site/error',
        ],*/
        'urlManager' => [
//            'scriptUrl'=>'/backend/index.php',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'http://localhost/gii',
                'http://localhost/index?r=gii',
            ],
        ],
    ],
    'params' => $params,
];
