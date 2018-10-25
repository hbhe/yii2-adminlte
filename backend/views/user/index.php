<?php

use common\models\User;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '帐号管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'username',
                'value' => function ($model, $key, $index, $column) {
                    return $model->username;
                },
                'headerOptions' => array('style' => 'width:100px;'),
            ],

            [
                'attribute' => 'name',
                'value' => function ($model, $key, $index, $column) {
                    return $model->name;
                },
                'headerOptions' => array('style' => 'width:80px;'),
            ],

            [
                'attribute' => 'mobile',
                'value' => function ($model, $key, $index, $column) {
                    return $model->mobile;
                },
                'headerOptions' => array('style' => 'width:120px;'),
            ],

            [
                'class' => '\hbhe\grid\ToggleColumn',
                'attribute' => 'status',
                'action' => 'toggle-status',
                'onText' => '禁用',
                'offText' => '启用',
                'displayValueText' => true,
                'onValueText' => '已禁用',
                'offValueText' => '已启用',
                'iconOn' => 'stop',
                'iconOff' => 'stop',
                // Uncomment if  you don't want AJAX
                'enableAjax' => false, // 使用pjax时要注掉或设为true
                //'visible' => YII_ENV_DEV,
                'confirm' => function ($model, $toggle) {
                    if ($model->status == User::STATUS_NOT_ACTIVE) {
                        return "确认启用: {$model->username}({$model->id})?";
                    } else {
                        return "确认禁用: {$model->username}({$model->id})?";
                    }
                },
                'headerOptions' => array('style' => 'width:80px;'),
            ],

            [
                'label' => '角色',
                'value' => function ($model, $key, $index, $column) {
                    return implode(',', $model->getRoleNames());
                },
                'headerOptions' => array('style' => 'width:120px;'),
            ],

            [
                'attribute' => 'created_at',
                'filter' => false,
                'headerOptions' => array('style' => 'width:140px;'),
            ],

            [
                'filter' => false,
                'attribute' => 'logged_at',
                'headerOptions' => array('style' => 'width:140px;'),
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'headerOptions' => array('style' => 'width:80px;'),
            ],

        ],
    ]); ?>

</div>

