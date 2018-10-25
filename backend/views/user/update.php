<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = '更新：' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '账号管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="user-update">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
