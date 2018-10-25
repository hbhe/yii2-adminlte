<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AccessLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Access Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="access-log-view">

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
            'user_id',
            'username',
            'ip',
            'detail:ntext',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>

<?php
/*
<?=  DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute' => 'id',
            'captionOptions' => ['width' => '20%'],
        ],
        [
            'attribute' => 'check_status',
            'value' => common\models\MktPostComplain::getPostComplainOption($model->reason) ,
        ],
        [
            'label' => '图片',
            //'value' => "<img src='". \Yii::$app->imagemanager->getImagePath($model->img_id, 160, 80, 'inset') ."'>",
            //'format'=> 'html',
            'value' => \Yii::$app->imagemanager->getImagePath($model->img_id, 160, 80, 'inset'),
            'format' => ['image', ['width'=>'100','height'=>'100']],
        ],
        [
            'attribute' => 'logo_id',
            'value' => $model->getLogoUrl(),
            'format' => ['image'],
            //'format' => ['image', ['width'=>'100','height'=>'100']],
        ],
    ],
])
*/
