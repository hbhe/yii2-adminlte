<?php
return [
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'enableStrictParsing' => true,
    'showScriptName' => false,
    'rules' => [
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/member'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'extraPatterns' => [
                'GET,POST login' => 'login',
                'GET,POST login-by-verify-code' => 'login-by-verify-code',
                'PUT reset-password' => 'reset-password',
                'GET,POST send-verify-code' => 'send-verify-code',
                'GET about-me' => 'about-me',
            ],
            'except' => ['delete'],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'pluralize' => false,
            'controller' => ['v1/article-category'],
            'except' => ['delete', 'update', 'create'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/article'],
            'except' => ['delete', 'update', 'create'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
        ],

        [
            'class' => 'yii\rest\UrlRule',
            'controller' => ['v1/agent-team'],
            'tokens' => [
                '{id}' => '<id:>',
            ],
            'extraPatterns' => [
                'POST picture-upload' => 'picture-upload',
                'POST picture-update/{id}' => 'picture-update',
                'POST picture-update-image/{id}' => 'picture-update-image',
                'OPTIONS picture-update/{id}' => 'options',
                'PUT update-status/{id}' => 'update-status',
                'OPTIONS update-status/{id}' => 'options',
            ]
        ],

    ],
];
