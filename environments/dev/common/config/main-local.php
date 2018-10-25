<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2adminlte',
            'username' => 'root',
            'password' => '',
            'tablePrefix' => 'ya_',
            'charset' => 'utf8',
            'enableSchemaCache' => !YII_DEBUG,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            //'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.qq.com',
                'username' => 'jack@qq.com',
                'password' => 'xx',
                'port' => '465',
                'encryption' => 'ssl',
            ],

            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['jack@qq.com' => 'admin'],
            ],

        ],

        'sm' => new \Overtrue\EasySms\EasySms([
            'timeout' => 5.0,
            'default' => [
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
                'gateways' => [
                    YII_DEBUG ? 'errorlog' : 'aliyun',
                    //'aliyun',
                    //'alidayu',
                ],
            ],
            'gateways' => [
                'errorlog' => [
                    'file' => Yii::getAlias('@common/runtime/easy-sms.log'),
                ],

                'aliyun' => [
                    'access_key_id' => 'LTAIvmHhER0EmctM',
                    'access_key_secret' => 'XCORzMk9y2X1PnwHl5Ghe2pa5qc9Ap',
                    'sign_name' => 'R5X定制商城',
                ],

                'yunpian' => [
                    'api_key' => 'xxx',
                ],

                'alidayu' => [
                    'app_key' => '23466963',
                    'app_secret' => 'e9ae28423c937c952e33be106273bed0',
                    'sign_name' => 'xx',
                ],

            ],
        ]),
    ],
];
