<?php

namespace common\modules\content\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%article_category}}".
 *
 * @property integer $id
 * @property string $title
 * @property integer $sort_order
 * @property string $created_at
 * @property string $updated_at
 */
class ArticleCategory extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_order'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 1024],
            [['sort_order'], 'default', 'value' => 0],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'sort_order' => '排序',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getArticles($conditions = [])
    {
        return $this->hasMany(Article::className(), ['article_category_id' => 'id'])->where($conditions);
    }

    public function getArticlesCount($conditions = [])
    {
        return $this->getArticles($conditions)->count();
    }

    public function afterDelete()
    {
        foreach ($this->articles as $model) {
            $model->delete();
        }
        parent::afterDelete();
    }

}
