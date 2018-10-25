<?php
namespace rest\modules\v1\controllers;

use common\models\Article;
use common\models\ArticleCategory;
use common\models\ArticleCategorySearch;
use common\models\ArticleSearch;
use noam148\imagemanager\models\ImageManager;
use rest\controllers\ActiveController;
use rest\models\Need;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Class ArticleCategoryController
 * @package rest\modules\v1\controllers
 *
 * 资讯分类列表
 * 127.0.0.1/cc/rest/web/v1/article-categories
 *
 * 资讯分类详情
 * 127.0.0.1/cc/rest/web/v1/article-categories/1
 *
 */
class ArticleCategoryController extends ActiveController
{
    public $modelClass = 'common\models\ArticleCategory';

    public $searchModelClass = 'common\models\ArticleCategorySearch';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['delete']);

        return $actions;
    }

    public function prepareDataProvider($action)
    {
        $searchModel = new ArticleCategorySearch;
        return $searchModel->search(Yii::$app->request->queryParams);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $model;
    }

    public function findModel($id)
    {
        if (($model = ArticleCategory::find()->where(['id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The item ($id) does not exist.');
        }
    }

    public function optional()
    {
        return [
            'index',
            'view',
        ];
    }
}