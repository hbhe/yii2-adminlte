<?php
use common\models\User;
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel hide">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>Alexander Pierce</p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form hide">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => '', 'options' => ['class' => 'header']],
                    [
                        'label' => '概况',
                        'icon' => 'home',
                        'url' => ['/site/dashboard'],
                    ],

                    [
                        'label' => '文章管理',
                        'url' => '#',
                        'icon' => 'list',
                        'options' => ['class' => 'treeview'],
                        'visible' =>  Yii::$app->user->can('内容模块'),
                        'items' => [
                            [
                                'label' => '文章列表',
                                'icon' => 'angle-double-right',
                                'url' => ['/content/article/index'],
                                'active' => Yii::$app->controller->id == 'article' && in_array(Yii::$app->controller->action->id, ['index', 'create', 'update']),
                            ],

                            [
                                'label' => '文章分类',
                                'icon' => 'angle-double-right',
                                'url' => ['/content/article-category/index'],
                                'active' => Yii::$app->controller->id == 'article-category' && in_array(Yii::$app->controller->action->id, ['index', 'create', 'update']),

                            ],
                        ],
                    ],

                    [
                        'label' => '操作员管理',
                        'visible' =>  Yii::$app->user->can('后台用户模块') || Yii::$app->user->can('日志模块'),
                        'url' => '#',
                        'icon' => 'user-circle',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            [
                                'label' => '操作员列表',
                                'icon' => 'angle-double-right',
                                'url' => ['/user/index'],
                                'active' => Yii::$app->controller->id == 'user' && in_array(Yii::$app->controller->action->id, ['index', 'update']),
                                'visible' => Yii::$app->user->can('后台用户模块'),
                            ],

                            [
                                'label' => '操作日志',
                                'icon' => 'angle-double-right"',
                                'url' => ['/access-log/index'],
                                'visible' =>  Yii::$app->user->can('日志模块'),
                            ],
                        ],
                    ],

                    [
                        'label' => '角色权限管理',
                        'visible' =>  Yii::$app->user->can('角色权限模块'),
                        'url' => '#',
                        'icon' => 'diamond',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            [
                                'label' => '角色管理',
                                'icon' => 'angle-double-right',
                                'url' => ['/admin/role/index'],
                                'active' => in_array(Yii::$app->controller->id, ['role']),
                            ],
                            [
                                'label' => '分配角色',
                                'icon' => 'angle-double-right',
                                'url' => ['/admin/assignment/index'],
                                'active' => in_array(Yii::$app->controller->id, ['assignment']),
                            ],
                            [
                                'label' => '菜单管理',
                                'icon' => 'angle-double-right',
                                'url' => ['/admin/menu/index'],
                                'visible' => false,
                            ],
                            [
                                'label' => '权限管理',
                                'icon' => 'angle-double-right',
                                'url' => ['/admin/route/index'],
                                'visible' => false,
                            ],
                        ],
                    ],
                    [
                        'label' => '网站设置',
                        'icon' => 'gear',
                        'url' => ['/settings/index'],
                        'active' => in_array(Yii::$app->controller->id, ['settings']),
                        'visible' =>  Yii::$app->user->can('参数设置模块'),
                    ],

                    [
                        'label' => '键值对',
                        'icon' => 'list',
                        'url' => ['/ks/key-storage/index'],
                        'visible' =>  Yii::$app->user->can(User::ROLE_ADMINISTRATOR),
                    ],

/*
                    ['label' => 'Gii', 'icon' => 'file', 'url' => ['/gii'], 'visible' =>  YII_ENV_DEV],
                    ['label' => 'Debug', 'icon' => 'bug', 'url' => ['/debug'], 'visible' =>  YII_ENV_DEV],

                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    [
                        'label' => 'Some tools',
                        'icon' => 'share',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Gii', 'icon' => 'file-code', 'url' => ['/gii'],],
                            ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
                            [
                                'label' => 'Level One',
                                'icon' => 'circle',
                                'url' => '#',
                                'items' => [
                                    ['label' => 'Level Two', 'icon' => 'circle', 'url' => '#',],
                                    [
                                        'label' => 'Level Two',
                                        'icon' => 'circle',
                                        'url' => '#',
                                        'items' => [
                                            ['label' => 'Level Three', 'icon' => 'circle', 'url' => '#',],
                                            ['label' => 'Level Three', 'icon' => 'circle', 'url' => '#',],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
*/
                ],
            ]
        ) ?>

    </section>

</aside>
