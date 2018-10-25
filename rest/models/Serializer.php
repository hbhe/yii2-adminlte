<?php

namespace rest\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;

class Serializer extends \yii\rest\Serializer
{
    public $collectionEnvelope = 'items';

    public $metaEnvelope = 'pagination';

    public $linksEnvelope = 'links';

    /**
     * Serializes the validation errors in a model.
     * @param Model $model
     * @return array the array representation of the errors
     */
    protected function serializeModelErrors($model)
    {
        $this->response->setStatusCode(422, 'Data Validation Failed.');
        $result = [];
        foreach ($model->getFirstErrors() as $name => $message) {
            $result[] = [
                'field' => $name,
                'message' => $message,
            ];
        }

        return $result;
    }

}
