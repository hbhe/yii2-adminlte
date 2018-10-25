<?php

namespace common\base;

use Yii;
use yii\base\InlineAction;
use yii\helpers\Url;

class GridView extends \yii\grid\GridView
{
    public $emptyText = false;
}
