<?php

namespace common\behaviors;

use common\models\AccessLog;
use Yii;
use yii\base\Behavior;
use yii\web\User;

class AfterLoginBehavior extends Behavior
{
    /**
     * @var string
     */
    public $attribute = 'logged_at';


    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            User::EVENT_AFTER_LOGIN => 'afterLogin'
        ];
    }

    /**
     * @param $event \yii\web\UserEvent
     */
    public function afterLogin($event)
    {
        $user = $event->identity;
        $user->{$this->attribute} = date('Y-m-d H:i:s');
        if (!$user->save(false)) {
            Yii::error([__METHOD__, __LINE__, $user->errors]);
        }

        AccessLog::log("登录成功");
    }
}
