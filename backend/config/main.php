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
    'defaultRoute' => 'site/dashboard',
    'name' => '后台管理',
    'bootstrap' => ['log'],

    // DEMO演示时禁止某些操作
    'on beforeAction' => function ($event) {
        if ($event->action->controller->id == 'role' && in_array($event->action->id, ['assign', 'remove', 'delete', 'update']) && in_array(Yii::$app->request->get('id'), ['超级管理员', '管理员', '操作员'])) {
            echo 'DEMO中请勿修改默认角色, 请创建新的角色进行体验!';
            Yii::$app->end();
        }
        if ($event->action->controller->id == 'assignment' && in_array($event->action->id, ['assign', 'revoke']) && in_array(Yii::$app->request->get('id'), [1, 2, 3])) {
            echo 'DEMO中请勿修改默认角色, 请创建新的角色进行体验!';
            Yii::$app->end();
        }
        if ($event->action->controller->id == 'user' && in_array($event->action->id, ['toggle-status', 'delete', 'update']) && in_array(Yii::$app->request->get('id'), [1, 2, 3])) {
            echo 'DEMO中请勿修改默认用户, 请创建新的用户进行体验!';
            Yii::$app->end();
        }
        if ($event->action->controller->id == 'user' && in_array($event->action->id, ['account']) && in_array(Yii::$app->user->id, [1, 2, 3]) && Yii::$app->request->isPost) {
            echo 'DEMO中请勿修改默认用户, 请创建新的用户进行体验!';
            Yii::$app->end();
        }
        if (in_array($event->action->controller->id, ['article', 'article-category']) && in_array($event->action->id, ['delete']) && in_array(Yii::$app->request->get('id'), [1, 2, 3]) && Yii::$app->request->isPost) {
            echo 'DEMO中请勿修改默认内容, 请创建新的文章或分类进行体验!';
            Yii::$app->end();
        }
    },

    'container' => [
        'definitions' => [
            'yii\widgets\LinkPager' => ['maxButtonCount' => 10],
            'yii\data\Pagination' => ['defaultPageSize' => 20, ], // 'validatePage' => false
            'yii\grid\GridView' => [
                'layout' => "{summary}\n{items}\n{pager}",
            ],
            'yii\grid\ActionColumn' => [
                'template' => '{view} {update} {delete}',
            ],
            'yii\widgets\DetailView' => [
                'options' => ['class' => 'table table-bordered detail-view'],
            ],
        ],
    ],

    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            //'layout' => 'left-menu',
            //'mainLayout' => '@app/views/layouts/main-rbac.php',
            'menus' => [
                'assignment' => [
                    'label' => '角色分配'
                ],
                'user' => null, // disable menu
            ],
        ],

        // noam148图片管理
        'imagemanager' => [
            'class' => 'noam148\imagemanager\Module',
            //set accces rules ()
            'canUploadImage' => true,
            'canRemoveImage' => function () {
                return true;
            },
            'deleteOriginalAfterEdit' => false, // false: keep original image after edit. true: delete original image after edit
            // Set if blameable behavior is used, if it is, callable function can also be used
            'setBlameableBehavior' => false,
            //add css files (to use in media manage selector iframe)
            'cssFiles' => [
                //'https://cdn.bootcss.com/font-awesome/4.6.3/css/font-awesome.min.css',
            ],
        ],

        // 内容管理
        'content' => [
            'class' => 'common\modules\content\backend\Module',
        ],

        // 键值对(key-value)列表
        'ks' => [
            'class' => 'hbhe\settings\Module',
        ],

    ],

    'components' => [
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-green',
                ],
            ],
        ],

        'request' => [
            'csrfParam' => '_csrf-backend',
        ],

        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'as afterLogin' => 'common\behaviors\AfterLoginBehavior',
            'as beforeLogout' => 'common\behaviors\BeforeLogoutBehavior',
        ],

        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => require(__DIR__.'/_urlManager.php'),

        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
        ],
    ],

    'params' => $params,

    'as globalAccess' => [
        'class' => '\common\behaviors\GlobalAccessBehavior',
        'rules' => [
            [
                'controllers' => ['gii/*', 'debug/*'],
                'allow' => true,
                'roles' => ['?', '@'],
            ],

            [
                'controllers' => ['site'],
                'actions' => ['login'],
                'allow' => true,
                'roles' => ['?'],
            ],

            [
                'controllers' => ['site'],
                'allow' => true,
                'roles' => ['@'],
            ],

            [
                'controllers' => ['user'],
                'allow' => true,
                'actions' => ['profile', 'account', 'avatar-upload', 'avatar-delete'],
                'roles' => ['@'],
            ],

            [
                'controllers' => ['settings'],
                'allow' => true,
                'roles' => ['内容模块'],
            ],

            [
                'controllers' => ['access-log'],
                'actions' => ['index'],
                'allow' => true,
                'roles' => ['日志模块'],
            ],

            [
                'controllers' => ['user'],
                'allow' => true,
                'roles' => ['后台用户模块'],
            ],

            [
                'controllers' => ['content/*'],
                'allow' => true,
                'roles' => ['内容模块'],
            ],

            [
                'controllers' => ['admin/*'],
                'allow' => true,
                'roles' => ['角色权限模块'],
            ],

            [
                'controllers' => ['imagemanager/*', 'i18n/*', 'file-manager-elfinder/*', 'ks/*'],
                'allow' => true,
                'roles' => ['@'],
            ],

            [
                'allow' => true,
                'roles' => [common\models\User::ROLE_ADMINISTRATOR],
            ],

        ]
    ]

];
