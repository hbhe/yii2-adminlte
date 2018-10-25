<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <p style="display:none;">
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'auth_key',
            'access_token',
            'password_hash',
            'oauth_client',
            'oauth_client_user_id',
            'email:email',
            'status',
            'created_at',
            'updated_at',
            'logged_at',
            'sid',
            'parent_id',
            'channel_role',
            'name',
            'mobile',
            'id_no',
            'title',
            'address',
            'area',
            'lat',
            'lon',
            [
                'label' => '图片',
                //'value' => "<img src='". \Yii::$app->imagemanager->getImagePath($model->img_id, 160, 80, 'inset') ."'>",
                //'format'=> 'html',
                'value' => \Yii::$app->imagemanager->getImagePath($model->img_id, 160, 80, 'inset'),
                'format'=> 'image',
            ],

            'sort_order',
        ],
    ]) ?>

</div>
