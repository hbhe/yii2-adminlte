<?php
use hbhe\settings\models\FormWidget;
$this->title = empty($title) ? Yii::t('backend', 'Application settings') : $title;
?>

<?= \yii\bootstrap\Nav::widget([
    'options' => [
        'class' => 'nav nav-tabs',
        'style' => 'margin-bottom: 15px'
    ],
    'items' => [
        [
            'label'   => '主题设置',
            'url'     => ['/settings/index'],
        ],
        [
            'label'   => 'DEMO参数',
            'url'     => ['/settings/demo'],
            'active' => Yii::$app->controller->action->id == 'demo',
        ],
    ]
]) ?>

<?php echo FormWidget::widget([
    'model' => $model,
    'formClass' => '\yii\bootstrap\ActiveForm',
    'submitText' => Yii::t('backend', 'Save'),
    'submitOptions' => ['class' => 'btn btn-primary']
]); ?>

