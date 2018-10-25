<?php
namespace rest\controllers;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\web\NotFoundHttpException;

/**
 * Class ActiveController
 * @package rest\models
 */
class ActiveController extends \yii\rest\ActiveController
{
    public $serializer = 'rest\models\Serializer';

    public $searchModelClass;

    public function init()
    {
        parent::init();
        //\Yii::$app->user->enableSession = false;
    }

    public function beforeAction($action)
    {
        Yii::info(['INIT', $this->route, Yii::$app->request->get(), Yii::$app->request->post()]);
        return parent::beforeAction($action);
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    /*
     * 设置不需要认证就能访问的action ids
     */
    public function optional()
    {
        return [];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                [
                    // 支持在url中传token参数
                    'class' => QueryParamAuth::className(),
                    'tokenParam' => 'access-token',
                    'optional' => $this->optional(),
                ],
                [
                    // 也支持在请求的header中传token, header格式为Authorization: Bearer xxx (xxx代表access-token)
                    'class' => HttpBearerAuth::className(),
                    'optional' => $this->optional(),
                ],
            ],
            'optional' => $this->optional(),
        ];

        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter, 解决跨域问题
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page', 'X-Pagination-Total-Count', 'X-Pagination-Page-Count', 'X-Pagination-Per-Page'],
            ],
        ];

        // 对于Content-Type: application/json 的跨域请求, 浏览器会先放OPTIONS, 再发POST之类的实际请求. 如果先认证的话OPTIONS就不会通过, 所以将认证放在跨域之后
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        unset($behaviors['contentNegotiator']);
        return $behaviors;
    }

    protected function findModel($id)
    {
        $modelClass = $this->modelClass;

        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The model $modelClass ($id) does not exist.');
        }
    }

    public function prepareDataProvider($action)
    {
        if (empty($this->searchModelClass)) {
            throw new \Exception('Search model class CAN NOT be empty.');
        }
        $searchModel = new $this->searchModelClass();
        return $searchModel->search(Yii::$app->request->queryParams);
    }
}