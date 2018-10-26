<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        //'@hbhe/settings' => '@vendor/hbhe/yii2-settings',
    ],
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',

    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                /*
                'db'=>[
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'except'=>['yii\web\HttpException:*', 'yii\i18n\I18N\*'],
                    'prefix'=>function () {
                        $url = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->getUrl() : null;
                        return sprintf('[%s][%s]', Yii::$app->id, $url);
                    },
                    'logVars'=>[],
                    'logTable'=>'{{%system_log}}'
                ],
                */
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'logVars' => [],
                    'levels' => ['error', 'warning'],
                    //'levels' => ['error', 'warning', 'info'],
                    //'levels' => ['error', 'warning', 'profile', 'info', 'trace'],
                ],

                // 测试时打开, 上线后注掉
                'info-application' => [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['application'],
                    'logVars' => [],
                    'levels' => ['info'],
                ],
            ],
        ],

        'urlManagerBackend' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo' => Yii::getAlias('@backendUrl')
            ],
            require(Yii::getAlias('@backend/config/_urlManager.php'))
        ),
        'urlManagerFrontend' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo' => Yii::getAlias('@frontendUrl')
            ],
            require(Yii::getAlias('@frontend/config/_urlManager.php'))
        ),
        'urlManagerStorage' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo' => Yii::getAlias('@storageUrl')
            ],
            require(Yii::getAlias('@storage/config/_urlManager.php'))
        ),

        'authManager' => [
            //'class' => 'yii\rbac\PhpManager',
            'class' => 'yii\rbac\DbManager',
            //'cache' => 'cache',
            'itemTable' => '{{%rbac_auth_item}}',
            'itemChildTable' => '{{%rbac_auth_item_child}}',
            'assignmentTable' => '{{%rbac_auth_assignment}}',
            'ruleTable' => '{{%rbac_auth_rule}}'
        ],

        // 文件系统组件
        'fs' => [
            'class' => 'creocoder\flysystem\LocalFilesystem',
            'path' => '@storage/web/source'
            //'path' => '@backend/web/storage/source',
        ],

        // 此组件与 trntv\filekit\actions\UploadAction 配套使用
        'fileStorage' => [
            'class' => trntv\filekit\Storage::class,
            'baseUrl' => '@storageUrl/source',
            //'baseUrl' => '@backendUrl/storage/source',
           // 'filesystemComponent'=> 'fs', // 内部使用fs组件保存上传的文件
//            'filesystem'=> function() {
//                $adapter = new \League\Flysystem\Adapter\Local(Yii::getAlias('@storage/web/source'));
//                return new League\Flysystem\Filesystem($adapter);
//            },
            'filesystem' => [
                'class' => common\components\filesystem\LocalFlysystemBuilder::class,
                'path' => '@storage/web/source',
                //'path' => '@backend/web/storage/source',
            ],

//            'as log' => [
//                'class' => common\behaviors\FileStorageLogBehavior::class,
//                'component' => 'fileStorage'
//            ]
        ],

        'ks' => [
            'class' => hbhe\settings\models\KeyStorage::class
        ],

        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => yii\i18n\PhpMessageSource::class,
                    'basePath' => '@common/messages',
                ],
                '*' => [
                    'class' => yii\i18n\PhpMessageSource::class,
                    'basePath' => '@common/messages',
                    'fileMap' => [
                        'common' => 'common.php',
                        'backend' => 'backend.php',
                        'frontend' => 'frontend.php',
                    ],
                    //'on missingTranslation' => [backend\modules\translation\Module::class, 'missingTranslation']
                ],
                /* Uncomment this code to use DbMessageSource
                 '*'=> [
                    'class' => 'yii\i18n\DbMessageSource',
                    'sourceMessageTable'=>'{{%i18n_source_message}}',
                    'messageTable'=>'{{%i18n_message}}',
                    'enableCaching' => YII_ENV_DEV,
                    'cachingDuration' => 3600,
                    //'on missingTranslation' => ['\backend\modules\translation\Module', 'missingTranslation']
                ],
                */
            ],
        ],

        // 短信组件
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
                    'access_key_secret' => 'xxx',
                    'sign_name' => 'xxx',
                ],
            ],
        ]),

        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'linkAssets' => YII_ENV_DEV ? false : true,
            'appendTimestamp' => YII_ENV_DEV,
            'assetMap' => [
                'jquery.js' => '//cdn.bootcss.com/jquery/2.2.4/jquery.min.js',
                'bootstrap.css' => '//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css',
                'bootstrap.js' => '//cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js',
                'jquery-ui.css' => '//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.min.css',
                'jquery-ui.js' => '//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.min.js',
                'fontawesome-all.css' => '//cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css',
            ],
        ],

        // noam148图片显示组件
        'imagemanager' => [
            'class' => 'noam148\imagemanager\components\ImageManagerGetPath',
            //set media path (outside the web folder is possible)
            // 'mediaPath' => '/path/where/to/store/images/media/imagemanager',
            //'mediaPath' => '/backend/web/storage/items',
            'mediaPath' => Yii::getAlias('@backend/web/image-upload'),

            //path relative web folder to store the cache images
            'cachePath' => 'assets/image-cache',
            //use filename (seo friendly) for resized images else use a hash
            'useFilename' => true,
            //show full url (for example in case of a API)
            'absoluteUrl' => true,
            'databaseComponent' => 'db' // The used database component by the image manager, this defaults to the Yii::$app->db component
        ],

    ],

];
