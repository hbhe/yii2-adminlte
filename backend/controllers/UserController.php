<?php

namespace backend\controllers;

use common\models\User;
use common\models\UserSearch;
use hbhe\grid\actions\ToggleAction;
use Intervention\Image\ImageManagerStatic;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\models\AccountForm;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'toggle-status' => [
                'class' => ToggleAction::className(),
                'onValue' => User::STATUS_ACTIVE,
                'offValue' => User::STATUS_NOT_ACTIVE,
                'modelClass' => 'common\models\User',
                'attribute' => 'status',
                // Uncomment to enable flash messages
                'setFlash' => false,
            ],

            'avatar-upload' => [
                'class' => UploadAction::class,
                'deleteRoute' => 'avatar-delete',
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    if ($event->file) {
                        $file = $event->file;
                        $img = ImageManagerStatic::make($file->read())->fit(215, 215);
                        $file->put($img->encode());
                    } else {
                        $fs = $event->filesystem;
                        $img = ImageManagerStatic::make($fs->read($event->path))->fit(215, 215);
                        $fs->put($event->path, $img->encode());
                    }
                }
            ],
            'avatar-delete' => [
                'class' => DeleteAction::class,
                'on afterDelete' => function($event) {
                    $file = $event->file;
                    //$thumb_path = Yii::getAlias('@storage/web/source') . $file->getPath();
                    //unlink($thumb_path);
                }
            ]
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->setReturnUrl(Url::current());

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Yii::$app->user->setReturnUrl(Url::current());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        $model = new User();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (empty($model->password)) {
                $model->addError('password', '密码不能为空');
            } else if ($model->password != $model->password_confirm) {
                $model->addError('password_confirm', '输入密码不一致');
            } else {
                $model->setPassword($model->password);
                if ($model->save()) {
                    // 设置勾选的角色
                    $auth = Yii::$app->authManager;
                    $auth->revokeAll($model->getId());
                    if ($model->rbacRoleNames && is_array($model->rbacRoleNames)) {
                        foreach ($model->rbacRoleNames as $role) {
                            $auth->assign($auth->getRole($role), $model->getId());
                        }
                    }
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (isset($_POST['cancel'])) {
            return $this->goBack();
        }

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (!$model->password && !$model->password_confirm) {
            } else if ($model->validatePassword($model->password) && !$model->password_confirm) {
            } else if ($model->password != $model->password_confirm) {
                $model->addError('password_confirm', '输入密码不一致');
                return $this->render('update', [
                    'model' => $model,
                ]);
            } else {
                $model->setPassword($model->password);
            }
            if ($model->save()) {
                // 设置勾选的角色
                $auth = Yii::$app->authManager;
                $auth->revokeAll($model->getId());
                if ($model->rbacRoleNames && is_array($model->rbacRoleNames)) {
                    foreach ($model->rbacRoleNames as $role) {
                        $auth->assign($auth->getRole($role), $model->getId());
                    }
                }
                return $this->goBack();
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ($model = $this->findModel($id)) {
            $model->delete();
        }
        return $this->goBack();
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionProfile()
    {
        $model = Yii::$app->user->identity->userProfile;
        if ($model->load($_POST) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', '保存成功!');
            return $this->refresh();
        }
        return $this->render('profile', ['model' => $model]);
    }

    public function actionAccount()
    {
        $user = Yii::$app->user->identity;
        $model = new AccountForm();
        $model->username = $user->username;
        $model->email = $user->email;
        if ($model->load($_POST) && $model->validate()) {
            $user->username = $model->username;
            $user->email = $model->email;
            if ($model->password) {
                $user->setPassword($model->password);
            }
            $user->save();
            \Yii::$app->getSession()->setFlash('success', '保存成功!');
            return $this->refresh();
        }
        return $this->render('account', ['model' => $model]);
    }

}

