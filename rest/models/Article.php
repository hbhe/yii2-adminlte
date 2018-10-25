<?php

namespace rest\models;

use Yii;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property integer $id
 * @property integer $article_category_id
 * @property string $author
 * @property string $title
 * @property string $detail
 * @property integer $img_id
 * @property string $img_url
 * @property integer $sort_order
 * @property integer $status
 * @property integer $is_slide
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class Article extends \common\modules\content\common\models\Article
{
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['img_id'],
            $fields['img_url']
        );
        $fields[] = 'imageUrl';
        return $fields;
    }

    public function extraFields()
    {
        $fields = parent::extraFields();
        $fields[] = 'articleCategory';
        return $fields;
    }
}
