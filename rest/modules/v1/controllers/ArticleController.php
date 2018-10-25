<?php
namespace rest\modules\v1\controllers;

use rest\models\Article;
use rest\models\ArticleSearch;
use noam148\imagemanager\models\ImageManager;
use rest\controllers\ActiveController;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Class ArticleController
 * @package rest\modules\v1\controllers
 *
 * 文章列表
 * 127.0.0.1/yii2-adminlte/rest/web/v1/articles?status=1&article_category_id=1
 * 127.0.0.1/yii2-adminlte/rest/web/v1/articles?status=1
 *
 * 文章详情
 * http://127.0.0.1/yii2-adminlte/rest/web/v1/articles/1?expand=articleCategory
 *
 */
class ArticleController extends ActiveController
{
    public $modelClass = 'rest\models\Article';

    public $searchModelClass = 'rest\models\ArticleSearch';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['delete']);

        return $actions;
    }

    public function prepareDataProvider($action)
    {
        $searchModel = new ArticleSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['status' => 1]);
        return $dataProvider;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    public function findModel($id)
    {
        if (($model = Article::find()->where(['id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The item ($id) does not exist.');
        }
    }

    /*
     * 定义不需登录认证就可访问的页面
     */
    public function optional()
    {
        return [
            'index',
            'view',
        ];
    }
}