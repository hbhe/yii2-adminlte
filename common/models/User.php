<?php
namespace common\models;

use common\behaviors\AfterSignupBehavior;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $logged_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE = 0;
    const STATUS_NOT_ACTIVE = 1;

    const ROLE_USER = '操作员';
    const ROLE_MANAGER = '管理员';
    const ROLE_ADMINISTRATOR = '超级管理员';

    const EVENT_AFTER_SIGNUP = 'afterSignup';

    public $rbacRoleNames;

    public $password;
    public $password_confirm;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y-m-d H:i:s'),
            ],

            'auth_key' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'auth_key'
                ],
                'value' => Yii::$app->getSecurity()->generateRandomString()
            ],

            'afterSignup' => [
                'class' => AfterSignupBehavior::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', ], 'unique'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_NOT_ACTIVE]],
            [['mobile'],  'match' , 'pattern'=>'/^1\d{10}$/' , 'message' => '手机格式不正确'],
            [['password_plain', 'password_hash', 'email', 'name'], 'string'],

            [['username', 'password', ], 'string', 'min' => 1],
            [['username'], 'required', ],
            [['password_confirm'], 'string'],

            [['created_at', 'updated_at', 'logged_at'], 'safe'],
            [['rbacRoleNames'], 'each',
                'rule' => ['in', 'range' => ArrayHelper::getColumn(Yii::$app->authManager->getRoles(), 'name')]
            ],

        ];
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->rbacRoleNames =  \yii\helpers\ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser($this->id), 'name');
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $model = \Yii::createObject(UserProfile::className());
            $model->locale = Yii::$app->language;
            $model->link('user', $this);
            $this->trigger(self::EVENT_AFTER_SIGNUP);
        }
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => '账号',
            'email' => 'E-mail',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'logged_at' => '登录时间',
            'mobile' => '手机',
            'name' => '姓名',
            'password' => '密码',
            'password_confirm' => '密码确认',
            'rbacRoleNames' => '角色',
        ];
    }

    public function isSelf()
    {
        return Yii::$app->user->id == $this->id;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getRoleNames()
    {
        $roles = Yii::$app->authManager->getRolesByUser($this->id);
        $arr =  \yii\helpers\ArrayHelper::getColumn($roles, 'name');
        return $arr;
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_ACTIVE => '启用',
            self::STATUS_NOT_ACTIVE => '禁用',
        ];
    }
}
