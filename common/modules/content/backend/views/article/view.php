<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\modules\content\common\models\Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-view">

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
            [
                'attribute' => 'id',
                'captionOptions' => ['width' => '20%'],
            ],

            'article_category_id',
            'author',
            'title',
            'detail:ntext',
//            'img_id',
//            'img_url:url',
            'url:url',
            'sort_order',
            'status:boolean',

            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>

<div>
    <?php echo Html::img($model->imageUrl, ['class' => 'img-responsive']) . '<br/><br/>'; ?>
</div>

<div>
    <?php echo $model->detail; ?>
</div>
