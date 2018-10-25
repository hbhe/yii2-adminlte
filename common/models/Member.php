<?php

namespace common\models;

use common\models\Util;
use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%member}}".
 *
 * @property integer $id
 * @property string $sid
 * @property string $mobile
 * @property string $name
 * @property string $nickname
 * @property string $auth_key
 * @property string $access_token
 * @property string $password_plain
 * @property string $password_hash
 * @property string $email
 * @property integer $status
 * @property string $avatar_path
 * @property string $avatar_base_url
 * @property string $created_at
 * @property string $updated_at
 * @property string $logged_at
 * @property integer $pid
 */
class Member extends \common\models\ActiveRecord implements IdentityInterface
{
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 0;

    public $picture;

    public $verify_code;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'auth_key', 'access_token', 'sid', 'username'], 'safe'],
            [['id', 'status', 'pid'], 'integer'],
            [['created_at', 'updated_at', 'logged_at'], 'safe'],
            [['mobile'], 'string', 'max' => 16],
            [['name', 'nickname', 'auth_key'], 'string', 'max' => 32],
            [['access_token'], 'string', 'max' => 40],
            [['password_plain', 'password_hash', 'email', 'avatar_path', 'avatar_base_url'], 'string', 'max' => 255],
            [['mobile'], 'unique'],

            [['status'], 'default', 'value' => self::STATUS_ACTIVE],

            [['picture', 'verify_code', 'password'], 'safe'],

            [['mobile'], 'match', 'pattern' => '/^1\d{10}$/', 'message' => '手机格式不正确'],
            [['mobile'], 'number'],
            [['mobile'], 'required'],
            [['pid'], 'default', 'value' => 0],

            [['status',], 'filter', 'filter' => 'intval'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y-m-d H:i:s'),
            ],
            'sid' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'sid'
                ],
                'value' => self::generateSid(),
            ],
            'auth_key' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'auth_key'
                ],
                'value' => Yii::$app->getSecurity()->generateRandomString()
            ],

            'access_token' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'access_token'
                ],
                'preserveNonEmptyValues' => true,
                'value' => function () {
                    return Yii::$app->getSecurity()->generateRandomString(40);
                }
            ],

            'picture' => [
                'class' => UploadBehavior::className(),
                'attribute' => 'picture',
                'pathAttribute' => 'avatar_path',
                'baseUrlAttribute' => 'avatar_base_url',
                //'multiple' => true,
                //'uploadRelation' => 'uploadedFiles',
            ],

        ];
    }

    static public function generateSid()
    {
        return Yii::$app->getSecurity()->generateRandomString(16) . uniqid();
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '用户ID',
            'sid' => '加密ID',
            'mobile' => '手机',
            'name' => '姓名',
            'nickname' => '昵称',
            'auth_key' => 'Auth密钥',
            'access_token' => 'Token',
            'password' => '密码',
            'password_plain' => '密码',
            'password_hash' => '密码',
            'email' => 'Email',
            'status' => '状态',
            'avatar_path' => '头像',
            'avatar_base_url' => '头像',
            'created_at' => '注册时间',
            'updated_at' => '更新时间',
            'logged_at' => '最近登录',
            'pid' => '上级ID',
            'avatarImageUrl' => '头像',
            'picture' => '头像',
            'statusString' => '状态',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->active()
            ->andWhere(['id' => $id])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->active()
            ->andWhere(['or', ['mobile' => $token], ['access_token' => $token]])
            //->andWhere(['access_token' => $token])
            ->one();
    }


    public static function find()
    {
        return new MemberQuery(get_called_class());
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()
            ->active()
            ->andWhere(['or', ['mobile' => $username], ['username' => $username]])
            ->one();
    }

    public function isMe()
    {
        return (!Yii::$app->user->isGuest) && Yii::$app->user->id == $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    public function getPublicIdentity()
    {
        return $this->nickname;
    }

    public function getPassword()
    {
        return $this->password_plain;
    }

    public function setPassword($password)
    {
        $this->password_plain = $password;
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    public function getDefaultAvatarImageUrl()
    {
        //return Yii::getAlias('@web/static/images/avatar.jpg');
        //return Yii::getAlias('@frontendUrl/static/images/male.png');
        return Yii::getAlias('@storageUrl/images/avatar.png');
    }

    public function getAvatarImageUrl()
    {
        if (empty($this->avatar_base_url) && empty($this->avatar_path)) {
            return $this->getDefaultAvatarImageUrl();
        } else if (empty($this->avatar_path)) {
            return $this->avatar_base_url;
        }
        return $this->avatar_base_url . '/' . $this->avatar_path;
    }

    /*
     * 获取自己的邀请他人扫一下的二维码URL(http://127.0.0.1/yii2-adminlte/frontend/web/site/signup?pid=1)
     */
    public function getQrCodeImageUrl()
    {
        $filename = Yii::getAlias("@storage/web/images/qrcode/{$this->id}.png");
        if (file_exists($filename)) {
            return Yii::getAlias("@storageUrl/images/qrcode/{$this->id}.png");
        }
        require_once Yii::getAlias('@vendor/phpqrcode/phpqrcode.php');
        $url = Yii::getAlias("@frontendUrl/site/signup?pid={$this->id}");
        \QRcode::png($url, $filename); // save as file, \QRcode::png($url, Yii::getAlias('@runtime/xxx.png'));
        return Yii::getAlias("@storageUrl/images/qrcode/{$this->id}.png");
    }

    public static function getStatusArray()
    {
        return [
            self::STATUS_ACTIVE => '正常',
            self::STATUS_NOT_ACTIVE => '冻结',
        ];
    }

    public function getStatusString()
    {
        $arr = self::getStatusArray();
        return empty($arr[$this->status]) ? '' : $arr[$this->status];
    }

}

