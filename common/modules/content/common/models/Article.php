<?php

namespace common\modules\content\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

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
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 */
class Article extends \common\models\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_category_id', 'img_id', 'sort_order'], 'integer'],
            [['detail'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['author'], 'string', 'max' => 32],
            [['title'], 'string', 'max' => 1024],
            [['url'], 'url'],
            [['status'], 'boolean'],
            [['title', 'detail', 'article_category_id'], 'required'],
            [['img_url'], 'string', 'max' => 512],
            [['created_by', 'updated_by'], 'safe'],

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
            'article_category_id' => '分类',
            'author' => '发布者',
            'title' => '标题',
            'detail' => '内容',
            'img_id' => '图片',
            'img_url' => '图片',
            'url' => '外部链接',
            'sort_order' => '排序',
            'status' => '列表中显示',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'imageUrl' => '图片',
        ];
    }

    public function getArticleCategory() {
        return $this->hasOne(ArticleCategory::className(), ['id' => 'article_category_id']);
    }

    public function getImageUrl($width = 9999, $height = 9999)
    {
        return \Yii::$app->imagemanager->getImagePath($this->img_id, $width, $height); // get originate picture
    }

}
