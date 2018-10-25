<?php

use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use common\models\User;
/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
/*
*/
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['enableClientScript' => false]); ?>

    <?php echo $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?php if (Yii::$app->user->can(User::ROLE_ADMINISTRATOR)): ?>
    <?php echo $form->field($model, 'rbacRoleNames')->checkboxList(\yii\helpers\ArrayHelper::getColumn(Yii::$app->authManager->getRoles(), 'name', 'name')) ?>
    <?php endif; ?>

    <?php echo $form->field($model, 'name')->textInput(['maxlength' => 10]) ?>

    <?php if (!$model->isSelf()): ?>
        <?php echo $form->field($model, 'status')->dropDownList(User::getStatusOptions()); ?>
    <?php endif; ?>


    <?php echo $form->field($model, 'mobile')->textInput(['maxlength' => 11]) ?>

    <?php echo $form->field($model, 'password')->passwordInput() ?>

    <?php echo $form->field($model, 'password_confirm')->passwordInput() ?>


    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php echo Html::submitInput('取消', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


