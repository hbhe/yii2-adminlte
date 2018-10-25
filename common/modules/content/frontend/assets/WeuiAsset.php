<?php

namespace common\modules\content\frontend\assets;

use yii\web\AssetBundle;

class WeuiAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/content/frontend/assets/weui';

    public $css = [
        'dist/style/weui.min.css',
    ];
}
