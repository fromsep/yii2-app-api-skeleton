<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$redis = require __DIR__ . '/redis.php';

$config = [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
             'enableCsrfValidation' => false,
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'abcdefg',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response'     => [
            'format'    => 'json',
            'charset'   => 'UTF-8',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            // 'identityClass' => 'app\models\User',
            'identityClass' => 'app\models\Auth',
            // 'enableAutoLogin' => true,
            'enableSession' => false,
            'loginUrl'      => NULL
        ],
        'errorHandler' => [
//            'errorAction' => 'site/error',
//            'class'       => 'yii\web\ErrorHandler',
            'class'       => 'app\components\ApiErrorHandler'
        ],
        'mailer' => [
            'class'  => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
            // 'useFileTransport' => false,
            'transport' => [
                'class'      => 'Swift_SmtpTransport', //使用的类
                'host'       => 'smtp.exmail.qq.com', //邮箱服务一地址
                'username'   => '', //邮箱地址，发送的邮箱
                'password'   => '', //自己填写邮箱密码
                'port'       => '465',  //服务器端口
                'encryption' => 'ssl',  //加密方式
            ],
            'messageConfig'=>[
                'charset' => 'UTF-8', //编码
                'from'    => ['' => '错误邮件发送中心']  //邮件里面显示的邮件地址和名称
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'logVars' => [],
                    'except'  => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:401',
                        'app\exceptions\ParamsException',
                        'app\exceptions\BusinessException',
                    ],
                    'logFile' => '@runtime/logs/app/' . date('Ymd') . '.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['order'],
                    'logVars' => ['*'],
                    'logFile' => '@runtime/logs/order/' . date('Ymd') . '.log',
                ],
//                [
//                    'class' => 'yii\log\EmailTarget',
//                    'levels' => ['error', 'warning'],
//                    'message' => [
//                        //'from' => ['settlement@shoufuyou.com'],
//                        'to'        => [],
//                        'subject'   => '错误日志',
//                    ],
//                ],
            ],
        ],
        'db'    => $db,
        'redis' => $redis,

        'urlManager' => [
            'enablePrettyUrl' => true,
//            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
