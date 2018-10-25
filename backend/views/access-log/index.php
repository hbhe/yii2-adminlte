<?php

use common\base\ActiveForm;
use common\base\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AccessLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '操作日志';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="access-log-index">

        <h1 style="display:none;"><?= Html::encode($this->title) ?></h1>
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?php //echo Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?php $form = ActiveForm::begin([
            'fieldConfig' => ['enableLabel' => false],
            'enableClientScript' => false,
        ]); ?>

        <?php $this->beginBlock('panel'); ?>
        <div class="grid-panel form-group row">
            <div class="col-md-12">
                <?php echo Html::submitInput('删除', ['name' => 'delete', 'class' => 'btn btn-primary',]) // 'data' => ['method'=> 'post', 'confirm' => '确认删除?'] ?>
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
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'layout' => "{summary}\n{items}\n<div>{$this->blocks['panel']}</div>\n{pager}",
            'options' => ['id' => 'grid_id'], //'class' => 'table-responsive'
            'columns' => [
                ['class' => 'yii\grid\CheckboxColumn'],

                [
                    'attribute' => 'id',
                    'headerOptions' => array('style'=>'width:50px;'),
                ],
                [
                    'attribute' => 'user_id',
                    'label' => '操作者ID',
                    'headerOptions' => array('style'=>'width:80px;'),
                ],

                [
                    'attribute' => 'user.username',
                    'value' => function ($model, $key, $index, $column) {
                        return ArrayHelper::getValue($model, 'user.username');
                    },
                    'headerOptions' => array('style'=>'width:120px;'),
                ],

                'ip',
                [
                    'attribute' => 'detail',
                    'format' => 'raw',
                    'headerOptions' => array('style'=>'width:400px;'),
                ],

                'created_at',
                // 'updated_at',

                [
                    'class' => 'yii\grid\ActionColumn',
                    //'template' => '{update} {view} {delete}',
                    'template' => '{delete}',
                    'visible' => false,
                ]
            ],
        ]); ?>

        <?php ActiveForm::end(); ?>

    </div>


<?php
/*
<?=  GridView::widget([
	'layout' => "<div>{summary}\n{items}\n{pager}</div>",
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'options' => ['class' => 'table-responsive'],
	'tableOptions' => ['class' => 'table table-striped'],  
	'columns' => [     

		['class' => yii\grid\CheckboxColumn::className()],

        'created_at:date',  
		[
			'label' => 'Office',
			'value'=>function ($model, $key, $index, $column) {
				return empty($model->office->title) ? '' : $model->office->title;
				return Yii::$app->formatter->asCurrency($model->amount/100);
				return MItem::getItemCatName($model->cid);
				return "￥".sprintf("%0.2f", $model->feesum/100);
			},
			'filter'=> false,
            'format' => 'currency',
			'filter'=> MItem::getItemCatName(),
			'headerOptions' => array('style'=>'width:80px;'),    
			'visible'=>Yii::$app->user->identity->openid == 'admin',          
		],

        [
            'attribute' => 'image_url',
            'format' => ['image', ['width'=>'32', 'height'=>'32']],
        ],

        [
            'attribute' => 'photo_id',
            'format' => ['image', ['width'=>'32', 'height'=>'32']],
            'value'=>function ($model, $key, $index, $column) {
                return \Yii::$app->imagemanager->getImagePath($model->photo_id);
            },
        ],

        [
            'label' => 'Shop',
            'format' => 'raw',
            'value'=>function ($model, $key, $index, $column) {
                return Html::a($model->sid, 'http://baidu.com', array("target" => "_blank"));
            },
        ],

        [
            'label' => 'avator',
            'format'=>'html',
            'value'=>function ($model, $key, $index, $column) { 
                if (empty($model->wxUser->headimgurl))
                    return '';
                $headimgurl = Html::img(\common\wosotech\Util::getWxUserHeadimgurl($model->wxUser->headimgurl, 46), ['class' => "img-responsive img-circle"]);
                return Html::a($headimgurl, ['/xg-member/index', 'openid' => $model->openid]);
            },
        ],

		[
			'label' => 'Post Image',
			'format'=>'html',
			'value'=>function ($model, $key, $index, $column) { 
				return Html::a($model->postResponseCount, ['post-response/index', 'post_id'=>$model->id]);
				return Html::a(Html::img(Url::to($model->getPicUrl()), ['width'=>'75']), $model->getPicUrl());
			},
		],

		[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{update} {view} {delete}',
			'options' => ['style'=>'width: 100px;'],
			'buttons' => [
				'update' => function ($url, $model) {
					return Html::a('<i class="glyphicon glyphicon-pencil"></i>', $url, [
						'class' => 'btn btn-xs btn-primary',
						'title' => Yii::t('plugin', 'Update'),
					]);
				},
				'view' => function ($url, $model) {
					return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', $url, [
						'class' => 'btn btn-xs btn-warning',
						'title' => Yii::t('plugin', 'View'),
					]);
				},
				'delete' => function ($url, $model) {
					return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
						'class' => 'btn btn-xs btn-danger',
						'data-method' => 'post',
						'data-confirm' => Yii::t('plugin', 'Are you sure to delete this item?'),
						'title' => Yii::t('plugin', 'Delete'),
						'data-pjax' => '0',
					]);
				},
			]
		],
	]
]); 

*/