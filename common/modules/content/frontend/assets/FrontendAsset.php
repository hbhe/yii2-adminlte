<?php

namespace common\modules\content\frontend\assets;

use yii\web\AssetBundle;

class FrontendAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/content/frontend/assets';

    public $css = [
    ];

    public $js = [
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
