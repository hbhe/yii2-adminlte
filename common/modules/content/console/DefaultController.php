<?php
namespace common\modules\content\console;

use yii\helpers\Json;

class DefaultController extends \yii\console\Controller {

    public $defaultAction = 'index';

    // php yii content/default/index
    public function actionIndex() {
        echo 'Hello';
    }

}

