<?php

namespace common\models;

//use common\wosotech\Util;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%access_log}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $category
 * @property string $username
 * @property string $ip
 * @property string $detail
 * @property string $created_at
 * @property string $updated_at
 */
class AccessLog extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%access_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['category'], 'integer'],
            [['detail'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['username'], 'string', 'max' => 64],
            [['ip'], 'string', 'max' => 32],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false,
            ],

            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'username',
                ],
                'value' => function ($event) {
                    return Yii::$app->user->identity->username;
                },
            ],

            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'ip',
                ],
                'value' => Yii::$app->request->getUserIP(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category' => '类别',
            'user_id' => '操作者',
            'username' => '账号',
            'ip' => 'IP',
            'detail' => '操作内容',
            'created_at' => '操作时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    static public function log($detail)
    {
        $model = new AccessLog();
        $model->detail = $detail;
        if (!$model->save()) {
            Yii::error([__METHOD__, __LINE__, $model->errors]);
        }
    }

}
