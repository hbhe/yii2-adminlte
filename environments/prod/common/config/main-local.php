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
            'viewPath' => '@common/mail',
        ],
    ],
];
