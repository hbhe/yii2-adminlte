<?php

use common\modules\content\common\models\ArticleCategory;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\content\common\models\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '文章列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index">

    <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php $form = ActiveForm::begin([
        'fieldConfig' => ['enableLabel' => false],
        'enableClientScript' => false,
    ]); ?>

    <?php $this->beginBlock('panel'); ?>
        <div class="grid-panel form-group row">
            <div class="col-md-12">
                <?php echo Html::submitInput('显示', ['name' => 'show', 'class' => 'btn btn-primary', ]) // 'data' => ['confirm' => '确认显示?'] ?>
                <?php echo Html::submitInput('隐藏', ['name' => 'hide', 'class' => 'btn btn-primary', ]) ?>
            </div>

            <?php
                $js = <<<EOD
                $(".grid-panel .btn").click(function() {
                    var ids = $('#grid_id').yiiGridView('getSelectedRows');
                    if (ids.length == 0) {
                        alert("请至少勾选一条记录!");
                        return false;
                    }
                    return true;
                });
EOD;
                $this->registerJs($js, yii\web\View::POS_READY);
            ?>
        </div>
    <?php $this->endBlock(); ?>

    <?= GridView::widget([
        'options' => ['id' => 'grid_id'], // 'class' => 'table-responsive'
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{summary}\n{items}\n<div>{$this->blocks['panel']}</div>\n{pager}",
        //'tableOptions' => ['class' => 'table table-striped'],
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],
            [
                'attribute' => 'id',
                'headerOptions' => array('style' => 'width:60px;'),
                'filter' => false,
            ],

            [
                'attribute' => 'imageUrl',
                'format' => ['image', ['width' => '48',]], //'height'=>'32'
            ],

            [
                'attribute' => 'article_category_id',
                'value' => function ($model, $key, $index, $column) {
                    return $model->articleCategory->title;
                },
                'filter' => ArrayHelper::map(ArticleCategory::find()->all(), 'id', 'title'),
                'headerOptions' => array('style' => 'width:100px;'),
            ],

            [
                'attribute' => 'author',
                'filter' => false,
            ],

            [
                'attribute' => 'title',
                'format' => function($value) { return StringHelper::truncate($value, 8); },
            ],

            // 'detail:ntext',
            // 'img_id',
            // 'img_url:url',

            [
                'attribute' => 'sort_order',
                'filter' => false,
                'headerOptions' => array('style' => 'width:60px;'),
            ],

            [
                'attribute' => 'status',
                'format' => 'boolean',
                'headerOptions' => array('style' => 'width:80px;'),
            ],

            [
                'attribute' => 'created_at',
                'filter' => false,
                'headerOptions' => array('style' => 'width:180px;'),
            ],

            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {view} {delete}',
            ]
        ],
    ]); ?>


    <?php ActiveForm::end(); ?>

</div>


