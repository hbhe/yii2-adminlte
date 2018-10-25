<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-rest',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'rest\controllers',
    'defaultRoute' => 'site/index',

    'modules' => [
        'v1' => [
            'class' => 'rest\modules\v1\Module',
        ],
    ],

    'container' => [
        'definitions' => [
            'yii\data\Pagination' => ['defaultPageSize' => 10, 'validatePage' => false], // 默然每页10条记录
        ],
    ],

    'components' => [
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'rest\models\Member',
            'loginUrl' => null,
            'enableSession' => false,
            'identityCookie' => ['name' => '_identity-rest', 'httpOnly' => true],
        ],

        'session' => [
            // this is the name of the session cookie used for login on the rest
            'name' => 'advanced-rest',
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'request' => [
            'csrfParam' => '_csrf-rest',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],

        'response' => [
            'class' => 'yii\web\Response',
            'format' => yii\web\Response::FORMAT_JSON,
            'on beforeSend' => function ($event) {
                if (Yii::$app->request->isOptions) {
                    return;
                }
                $response = $event->sender;
                $response->data = [
                    'success' => $response->isSuccessful,
                    'data' => $response->data,
                ];
                $response->statusCode = 200;
            },
        ],

        'urlManager' => require(__DIR__.'/_urlManager.php'),
    ],

    'params' => $params,
];
