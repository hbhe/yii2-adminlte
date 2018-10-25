<?php

namespace rest\modules\v1\controllers;

use common\models\Util;
//use Intervention\Image\ImageManagerStatic;
use rest\controllers\ActiveController;
use rest\models\Member;
use rest\models\MemberSearch;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;
use Yii;
use yii\base\Exception;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Class MemberController
 * @package rest\modules\v1\controllers
 *
 * 手机或ID密码登录
 * http://127.0.0.1/yii2-adminlte/rest/web/v1/members/login?mobile=13900000001&password=123456
 *
 * 手机验证码登录
 * http://127.0.0.1/yii2-adminlte/rest/web/v1/members/login-by-verify-code?mobile=13900000001&verify_code=3456
 *
 * 注册(个人或代理)
 * POST 127.0.0.1/yii2-adminlte/rest/web/v1/members
 *
 * 127.0.0.1/yii2-adminlte/rest/web/v1/members/about-me?access-token=13900000001
 *
 * 发送短信校验码
 * 127.0.0.1/yii2-adminlte/rest/web/v1/members/send-verify-code?mobile=13900000001&template=SMS_137580007
 *
 * 验证短信校验码
 * 127.0.0.1/yii2-adminlte/rest/web/v1/members/validate-verify-code?mobile=13900000001&verify_code=1234
 *
 * 个人基础信息修改, 如昵称, 手机号(修改手机号时必须同时提供检验码)
 * PUT 127.0.0.1/yii2-adminlte/rest/web/v1/members/1?access-token=token-13900000001
 *
 * 个人头像修改(即上传头像文件)
 * POST 127.0.0.1/yii2-adminlte/rest/web/v1/members/update-avatar?access-token=token-13900000001
 *
 * 修改密码
 * PUT 127.0.0.1/yii2-adminlte/rest/web/v1/members/update-password?access-token=token-13900000001
 *
 * 忘记密码重置
 * PUT 127.0.0.1/yii2-adminlte/rest/web/v1/members/reset-password
 *
 */
class MemberController extends ActiveController
{
    /**
     * @var string
     */
    public $modelClass = 'rest\models\Member';

    /**
     * @var string
     */
    public $searchModelClass = 'rest\models\MemberSearch';

    /**
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['view']);

        $actions['update-avatar'] = [
            'class' => UploadAction::className(),
            'multiple' => false,
            'fileparam' => 'file',
            'deleteRoute' => 'avatar-delete',
            'on afterSave' => function ($event) {
                $file = $event->file;
                //$img = ImageManagerStatic::make($file->read())->fit(80, 80);
                //$file->put($img->encode());
                $model = Yii::$app->user->identity;
                $model->detachBehavior('picture');
                $model->avatar_path = $file->getPath();
                $model->avatar_base_url = Yii::$app->fileStorage->baseUrl;
                if (!$model->save()) {
                    Yii::error([__METHOD__, __LINE__, $model->errors]);
                }
            }
        ];

        $actions['avatar-delete'] = [
            'class' => DeleteAction::className(),
        ];

        return $actions;
    }

    /*
     * 定义可登录也可不登录的actions
     */
    public function optional()
    {
        return [
            'index',
            'view',
            'create',
            'login',
            'login-by-verify-code',
            'reset-password',
            'send-verify-code',
            'validate-verify-code',
        ];
    }

    /**
     * @param $action
     * @return array
     */
    public function prepareDataProvider($action)
    {
        $searchModel = new MemberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        return $dataProvider;
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     * @throws Exception
     */
    public function actionLogin()
    {
        $mobile = Yii::$app->request->post('mobile') ?: Yii::$app->request->get('mobile');
        $password = Yii::$app->request->post('password') ?: Yii::$app->request->get('password');

        $model = Member::find()
            ->andWhere(['or', ['mobile' => $mobile], ['id' => $mobile]])
            ->one();
        if (null === $model) {
            throw new ForbiddenHttpException('无此账号');
        }

        if (\common\models\Member::STATUS_NOT_ACTIVE === $model->status) {
            throw new ForbiddenHttpException('账号已冻结');
        }

        if (!$model->validatePassword($password)) {
            throw new ForbiddenHttpException('密码不正确');
        }
        return $model;
    }

    public function actionLoginByVerifyCode()
    {
        $mobile = Yii::$app->request->post('mobile') ?: Yii::$app->request->get('mobile');
        $verify_code = Yii::$app->request->post('verify_code') ?: Yii::$app->request->get('verify_code');

        $model = Member::find()
            ->andWhere(['or', ['mobile' => $mobile], ['id' => $mobile]])
            ->one();
        if (null === $model) {
            throw new ForbiddenHttpException('无此账号');
        }

        if (\common\models\Member::STATUS_NOT_ACTIVE === $model->status) {
            throw new ForbiddenHttpException('账号已冻结');
        }

        if (!Util::checkVerifyCode($mobile, $verify_code)) {
            throw new ForbiddenHttpException('校验码不正确');
        }
        return $model;
    }

    /**
     * 我的信息
     * @return Member
     */
    public function actionAboutMe()
    {
        $model = $this->findModel(Yii::$app->user->id);
        return $model;
    }

    /**
     * 注册
     * @return array|Member
     */
    public function actionCreate()
    {
        $model = new \rest\models\Member();
        $params = Yii::$app->request->post();

        if ($model->load($params, '') && $model->validate()) {
            if (empty($model->verify_code) || empty($model->password)) {
                throw new ForbiddenHttpException('密码、校验码或者邀请码不能为空');
            }
            if (strlen($model->password) < 6) {
                throw new ForbiddenHttpException('密码至少6位数字和字母组合');
            }

            if (!Util::checkVerifyCode($model->mobile, $model->verify_code)) {
                throw new ForbiddenHttpException('校验码不正确');
            }
            $model->setPassword($model->password);
            if (!$model->save()) {
                Yii::error([__METHOD__, __LINE__, $model->errors]);
            }
        }
        return $model;
    }

    /**
     * 发送校验码
     * @return mixed
     * @throws Exception
     */
    public function actionSendVerifyCode()
    {
        $mobile = Yii::$app->request->post('mobile') ?: Yii::$app->request->get('mobile');
        $template = Yii::$app->request->post('template') ?: Yii::$app->request->get('template', 'SMS_001');
        $params = [
            'mobile' => $mobile,
            'template' => $template,
        ];
        $resp = Json::decode(Util::sendVerifycodeAjax($params), true);
        if ($resp['code'] != 0) {
            throw new ForbiddenHttpException($resp['msg']);
        }
        return $resp;
    }

    /**
     * 检查校验码
     * @return mixed
     * @throws Exception
     */
    public function actionValidateVerifyCode()
    {
        $mobile = Yii::$app->request->post('mobile') ?: Yii::$app->request->get('mobile');
        $verify_code = Yii::$app->request->post('verify_code') ?: Yii::$app->request->get('verify_code');
        if (!Util::checkVerifyCode($mobile, $verify_code)) {
            throw new ForbiddenHttpException('校验码不正确');
        }
        return ['code' => 0];
    }

    /**
     * 修改个人昵称， 手机号(修改手机号时，必须提供校验码)
     * @param $id
     * @return Member
     */
    public function actionUpdate($id)
    {
        if ($id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('just can update own account');
        }
        $model = $this->findModel($id);
        $old_mobile = $model->mobile;
        $params = Yii::$app->request->post();
        if ($model->load($params, '') && $model->validate()) {
            // 如果手机号发生变化, 就检查手机校验码
            if ($old_mobile != $model->mobile) {
                if (empty($model->verify_code) || !Util::checkVerifyCode($model->mobile, $model->verify_code)) {
                    $model->addError('mobile', "手机校验码不正确");
                    return $model;
                }
            }
            $model->save(false);
        }

        return $model;
    }

    /**
     * 未登录状态下重置密码
     * @param $id
     * @return Member
     */
    public function actionResetPassword()
    {
        $mobile = Yii::$app->request->post('mobile');
        $verify_code = Yii::$app->request->post('verify_code');
        $password = Yii::$app->request->post('password');
        $model = Member::findOne(['mobile' => Yii::$app->request->post('mobile')]);
        if ($model === null) {
            throw new NotFoundHttpException('无效的用户');
        }
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            if (empty($verify_code) || !Util::checkVerifyCode($model->mobile, $verify_code)) {
                $model->addError('mobile', "手机校验码不正确");
                return $model;
            }
            if (empty($password)) {
                $model->addError('password', "密码不能为空!");
                return $model;
            }

            if ($model->password) {
                $model->setPassword($model->password);
            }
            $model->save(false);
        }

        return $model;
    }

    /**
     * 登录之后修改自己的密码, 必须提供老密码和新密码
     * @param $id
     * @return Member
     */
    public function actionUpdatePassword()
    {
        $model = Member::findOne(Yii::$app->user->id);
        if (empty(Yii::$app->request->post('old_password')) || empty(Yii::$app->request->post('new_password'))) {
            throw new HttpException('400', '密码不能为空');
        }
        if (!$model->validatePassword(Yii::$app->request->post('old_password'))) {
            throw new HttpException('400', '密码不正确');
        }
        $model->setPassword(Yii::$app->request->post('new_password'));
        $model->save(false);
        return $model;
    }

    /**
     * @param $id
     * @return \common\models\Member
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Member::findOne($id)) === null) {
            throw new NotFoundHttpException('此用户ID不存在.');
        }
        return $model;
    }

    protected function findMe($id)
    {
        return Yii::$app->user->identity;
    }

    /**
     * @param $id
     * @return \common\models\Member
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

}

