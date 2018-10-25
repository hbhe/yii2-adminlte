<?php
use common\modules\content\common\models\Article;
use common\modules\content\common\models\ArticleCategory;
use noam148\imagemanager\models\ImageManager;
use yii\db\Migration;
use yii\helpers\Console;
use yii\helpers\FileHelper;

class m180914_080817_init_content extends Migration
{
    public function up()
    {
        $faker = \Faker\Factory::create('zh_CN');

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
        }

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%article_category}}")->execute();
        $this->createTable('{{%article_category}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(1024)->comment('标题'),
            'sort_order' => $this->integer()->comment('排序'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%article_category}}', '文章分类');


        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%article}}")->execute();
        $this->createTable('{{%article}}', [
            'id' => $this->primaryKey(),
            'article_category_id' => $this->integer()->comment('分类ID'),
            'author' => $this->string(32)->comment('发布者'),
            'title' => $this->string(1024)->comment('标题'),
            'detail' => $this->text()->comment('内容'),
            'url' => $this->string(512)->comment('外部链接'),
            'img_id' => $this->integer()->comment('图片'), // 封面
            'img_url' => $this->string(512)->comment('图片'), // 优先
            'sort_order' => $this->integer()->comment('排序'),
            'status' => $this->smallInteger()->comment('显示'),// 0: 否 1:是
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
            'created_by' => $this->integer()->comment('创建人'),
            'updated_by' => $this->integer()->comment('修改人'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%article}}', '文章');
        $this->createIndex('article_category_id', '{{%article}}', ['article_category_id', 'sort_order']);

        if (Console::confirm('Seed content demo data?', true)) {
            $this->seed();
        }

        return true;
    }

    public function down()
    {
        $this->dropTable('{{%article}}');
        $this->dropTable('{{%access_log}}');

        return true;
    }

    public function seed()
    {
        $faker = \Faker\Factory::create('zh_CN');

        $articleCategories = ['新闻', '资讯'];
        foreach ($articleCategories as $articleCategory) {
            $model = new ArticleCategory();
            $model->setAttributes([
                'title' => $articleCategory,
            ], false);
            echo "\n insert ArticleCategory " . ($model->save() ? 'ok' : 'err' . print_r($model->errors, true)) . "\n";
        }

        $articleCategoryIds = ArticleCategory::find()->select('id')->column();
        for ($i = 0; $i < 50; $i++) {
            $model = new Article();
            $model->setAttributes([
                'article_category_id' => $articleCategoryIds[array_rand($articleCategoryIds)],
                'title' => $faker->catchPhrase,
                'detail' => $faker->randomHtml(),
                //'img_id' => $imageIds[array_rand($imageIds)],
                'author' => $faker->name,
                'status' => rand(0, 1),
            ], false);
            echo "\n insert Article " . ($model->save() ? 'ok' : 'err' . print_r($model->errors, true)) . "\n";
        }
    }
}

