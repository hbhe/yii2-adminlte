<?php

namespace common\behaviors;

use common\models\AccessLog;
use Yii;
use yii\base\Behavior;
use yii\web\User;

class BeforeLogoutBehavior extends Behavior
{
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            User::EVENT_BEFORE_LOGOUT => 'beforeLogout'
        ];
    }

    /**
     * @param $event \yii\web\UserEvent
     */
    public function beforeLogout($event)
    {
        AccessLog::log("退出登录");
    }
}
