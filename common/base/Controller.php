<?php
namespace common\base;

use Yii;
use yii\filters\AccessControl;

class Controller extends \yii\web\Controller
{
    public function actionAjaxBroker($args)
    {
        $args1 = json_decode($args, true);
        $args1['classname'] = strtr($args1['classname'], ['-' => '\\']);
        Yii::info([__METHOD__, $args, $args1]);
        return call_user_func(array($args1['classname'], $args1['funcname']), $args1['params']);
    }

    public function render($view, $params = [])
    {
        if (Yii::$app->request->isAjax) {
            return $this->getView()->renderAjax($view, $params, $this);
        }

        $content = $this->getView()->render($view, $params, $this);
        return $this->renderContent($content);
    }

    public function redirect($url, $statusCode = 302)
    {
        if (Yii::$app->request->isAjax) {
            // JSON response is expected in case of successful save
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['success' => true];
        }
        return parent::redirect($url, $statusCode);
    }

    public function goBack($defaultUrl = null)
    {
        if (Yii::$app->request->isAjax) {
            // JSON response is expected in case of successful save
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['success' => true];
        }

        return parent::goBack($defaultUrl);
    }

}

