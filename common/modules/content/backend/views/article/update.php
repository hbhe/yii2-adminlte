<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\content\common\models\Article */

$this->title = '更新：' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '文章列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="article-update">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
