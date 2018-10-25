<?php

namespace common\behaviors;

use common\models\AccessLog;
use common\models\User;
use Yii;
use yii\base\Behavior;

class AfterSignupBehavior extends Behavior
{
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            User::EVENT_AFTER_SIGNUP => 'afterSignup'
        ];
    }

    /**
     * @param $event \yii\web\UserEvent
     */
    public function afterSignup($event)
    {
        $user = $this->owner;
        AccessLog::log("成功创建用户: {$user->id}, {$user->username}");
    }
}
