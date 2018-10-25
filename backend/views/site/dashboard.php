<?php
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '概况';
$this->params['breadcrumbs'][] = $this->title;

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

<section class="content">
    <!-- Info boxes -->
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="ion ion-ios-personadd-outline"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asDecimal(10000); ?></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="ion ion-ios-personadd-outline"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Yesterday</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asDecimal(10000); ?></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="ion ion-ios-personadd-outline"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Current Month</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asDecimal(90000); ?></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Total</span>
                    <span class="info-box-number"><?= Yii::$app->formatter->asDecimal(User::find()->count()); ?></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
    <br />



    <div class="box-footer">
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <div class="description-block border-right">
                    <!--                    <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 17%</span>-->
                     <h5 class="description-header">TOTAL:
                         <span class="text-green"><?= Yii::$app->formatter->format(9000, 'decimal'); ?></span>
                     </h5>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 col-xs-6">
                <div class="description-block border-right">
                    <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 17%</span>
                    <h5 class="description-header">
                        <span class="text-red"><?= Yii::$app->formatter->format(1000, 'decimal'); ?></span>
                    </h5>
                    <span class="description-text">TODAY</span>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-xs-6">
                <div class="description-block border-right">
                    <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span>
                    <h5 class="description-header">
                        <span class="text-red"><?= Yii::$app->formatter->format(2000, 'decimal'); ?></span>
                    </h5>
                    <span class="description-text">YESTERDAY</span>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 col-xs-6">
                <div class="description-block border-right">
                    <span class="description-percentage text-green"><i class="fa fa-caret-down"></i> 20%</span>
                    <h5 class="description-header">
                        <span class="text-red"><?= Yii::$app->formatter->format(3000, 'decimal'); ?></span>
                    </h5>
                    <span class="description-text">MONTH</span>
                </div>
            </div>
        </div>
    </div>

<br/>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">TODO</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <ul class="products-list product-list-in-box">
            <li class="item">
                <div class="product-img hide">
                    <img src="" alt="Product Image"> <!-- dist/img/default-50x50.gif -->
                </div>
                <div class="product-info">
                    <a href="<?= Url::to(['/user/index'])?>" class="product-title">TODO
                        <span class="label label-warning pull-right"><?= '1' ?></span></a>
                    <span class="product-description hide">
                          todo
                    </span>
                </div>
            </li>
        </ul>
    </div>
    <!-- /.box-body -->
    <div class="box-footer text-center hide">
        <a href="javascript:void(0)" class="uppercase">View All Products</a>
    </div>
    <!-- /.box-footer -->
</div>
<!-- /.box -->

</section>


