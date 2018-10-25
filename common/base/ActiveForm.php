<?php
namespace common\base;

use Yii;
use yii\filters\AccessControl;

class ActiveForm extends \yii\bootstrap\ActiveForm
{
    public $enableClientValidation = false;

    public $options = [
        //'data-pjax' => true, // pjax 提交form
    ];

}

