<?php

namespace rest\models;

use Yii;
use yii\filters\RateLimitInterface;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;


/**
 * Class Member
 * @package rest\models
 */
class Member extends \common\models\Member implements RateLimitInterface //IdentityInterface,
{
    /**
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @return array
     */
    public function getRateLimit($request, $action)
    {
        return [
            ArrayHelper::getValue(Yii::$app->params, 'maxRateLimit', 60000),
            ArrayHelper::getValue(Yii::$app->params, 'perRateLimit', 60),
        ];
    }

    /**
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @return array|mixed
     */
    public function loadAllowance($request, $action)
    {
        if (false === ($value = yii::$app->cache->get([__CLASS__]))) {
            return $this->getRateLimit($request, $action);
        }
        return $value;
    }

    /**
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @param int $allowance
     * @param int $timestamp
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        yii::$app->cache->set([__CLASS__], [$allowance, $timestamp]);
    }

}

