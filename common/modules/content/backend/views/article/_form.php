<?php

use common\modules\content\common\models\ArticleCategory;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\content\common\models\Article */
/* @var $form yii\bootstrap\ActiveForm */
?>

    <div class="article-form">

        <?php $form = ActiveForm::begin(['enableClientScript' => false]); ?>

        <?php echo $form->field($model, 'article_category_id')->dropDownList(ArrayHelper::map(ArticleCategory::find()->all(), 'id', 'title')) ?>

        <!--    --><?php //echo $form->field($model, 'author')->textInput(['maxlength' => true]) ?>

        <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?php echo $form->field($model, 'img_id')->widget(\noam148\imagemanager\components\ImageManagerInputWidget::className(), [
            'cropViewMode' => 1,
            'aspectRatio' => (450 / 300),
            'showPreview' => true,
            'showDeletePickedImageConfirm' => false, //on true show warning before detach image
        ]); ?>

        <?php echo $form->field($model, 'detail')->widget(\dosamigos\tinymce\TinyMce::className(), [
            'id' => 'DESC_b_1_5_PLAIN_LAYOUT',
            'options' => [
                'rows' => 8,
            ],
            'language' => 'zh_CN',
            'clientOptions' => [
                'relative_urls' => false,
                'remove_script_host' => false,
                'convert_urls' => true,
                'file_browser_callback' => new yii\web\JsExpression("function(field_name, url, type, win) {
            window.open('" . yii\helpers\Url::to(['/imagemanager/manager', 'view-mode' => 'iframe', 'select-type' => 'tinymce']) . "&tag_name='+field_name,'','width=800,height=540 ,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=no');
        }"),
                'plugins' => [
                    "advlist autolink lists link charmap print preview anchor",
                    "searchreplace visualblocks code fullscreen",
                    "insertdatetime media table contextmenu paste image"
                ],
                'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
            ]
        ]); ?>


        <?php echo $form->field($model, 'status')->dropDownList([1 => '是', 0 => '否']) ?>

        <?php echo $form->field($model, 'url')->textInput()->hint('优先跳转外链,无外链时显示内容') ?>

        <?php echo $form->field($model, 'sort_order')->textInput() ?>

        <div class="form-group">
            <?php echo Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?php echo Html::submitInput('取消', ['name' => 'cancel', 'class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

