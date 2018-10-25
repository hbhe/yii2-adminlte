<?php

use common\models\Member;
use common\models\User;
use noam148\imagemanager\models\ImageManager;
use yii\db\Migration;
use yii\helpers\Console;
use yii\helpers\FileHelper;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        // 前端用户, 二次开发时需重新定义
        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%member}}")->execute();
        $this->createTable('{{%member}}', [
            'id' => $this->primaryKey()->comment('用户ID'),
            'sid' => $this->string(64)->comment('加密ID')->unique(),
            'username' => $this->string(64)->comment('账号')->unique(),
            'mobile' => $this->string(16)->comment('手机')->unique(),
            'name' => $this->string(32)->comment('姓名'),
            'nickname' => $this->string(32)->notNull()->defaultValue('')->comment('昵称'),
            'auth_key' => $this->string(32)->notNull()->comment('Auth密钥'),
            'access_token' => $this->string(40)->notNull()->comment('Token'),
            'password_plain' => $this->string()->comment('密码'),
            'password_hash' => $this->string()->comment('密码'),
            'email' => $this->string(),
            'status' => $this->smallInteger()->notNull()->defaultValue(Member::STATUS_ACTIVE),
            'avatar_path' => $this->string()->comment('头像'),
            'avatar_base_url' => $this->string()->comment('头像'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
            'logged_at' => $this->timestamp()->defaultValue(null)->comment('最近登录'),
            'pid' => $this->integer()->notNull()->defaultValue(0)->comment('上级ID'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%member}}', '用户');

        // 后台用户
        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%user_profile}}")->execute();
        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%user}}")->execute();
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(32)->notNull()->defaultValue('')->comment('账号'),
            'auth_key' => $this->string(32)->notNull(),
            'password_plain' => $this->string()->notNull()->defaultValue('')->comment('密码'),
            'password_hash' => $this->string()->comment('密码'),
            'password_reset_token' => $this->string(),
            'email' => $this->string(64)->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(User::STATUS_ACTIVE),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->comment('更新时间'),
            'logged_at' => $this->timestamp()->comment('最近登录'),
            'mobile' => $this->string(16)->notNull()->defaultValue('')->comment('手机'),
            'name' => $this->string(16)->comment('姓名'),
            'sort_order' => $this->integer()->comment('排序'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%user}}', '后台用户');

        $this->createTable('{{%user_profile}}', [
            'user_id' => $this->primaryKey(),
            'firstname' => $this->string(),
            'middlename' => $this->string(),
            'lastname' => $this->string(),
            'avatar_path' => $this->string(),
            'avatar_base_url' => $this->string(),
            'locale' => $this->string(32)->notNull(),
            'gender' => $this->smallInteger(1)
        ], $tableOptions);
        $this->addForeignKey('fk_user', '{{%user_profile}}', 'user_id', '{{%user}}', 'id', 'cascade', 'cascade');

        $this->insert('{{%user}}', [
            'id' => 1,
            'username' => 'webmaster',
            'email' => 'webmaster@example.com',
            'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('webmaster'),
            'name' => 'Jack',
        ]);
        $this->insert('{{%user}}', [
            'id' => 2,
            'username' => 'manager',
            'email' => 'manager@example.com',
            'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('manager'),
            'name' => 'Rose',
        ]);
        $this->insert('{{%user}}', [
            'id' => 3,
            'username' => 'user',
            'email' => 'user@example.com',
            'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('user'),
            'name' => 'Tom',
        ]);

        $this->insert('{{%user_profile}}', [
            'user_id' => 1,
            'locale' => Yii::$app->language,
            'firstname' => '',
            'lastname' => ''
        ]);
        $this->insert('{{%user_profile}}', [
            'user_id' => 2,
            'firstname' => '',
            'locale' => Yii::$app->language,
        ]);
        $this->insert('{{%user_profile}}', [
            'user_id' => 3,
            'firstname' => '',
            'locale' => Yii::$app->language,
        ]);

        // 创建角色和权限
        $this->createRolePermissions();

        Yii::$app->db->createCommand("DROP TABLE IF EXISTS {{%access_log}}")->execute();
        $this->createTable('{{%access_log}}', [
            'id' => $this->primaryKey(),
            'category' => $this->integer()->notNull()->defaultValue(1),
            'user_id' => $this->integer()->comment('操作者'),
            'username' => $this->string(64)->comment('账号'),
            'ip' => $this->string(32)->comment('IP'),
            'detail' => $this->text()->comment('操作内容'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->comment('更新时间'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%access_log}}', '操作日志');
        $this->createIndex('category', '{{%access_log}}', ['category']);

        if (Console::confirm('Seed demo data?', true)) {
            $this->seed();
        }
        return false;

    }

    public function down()
    {
        $faker = \Faker\Factory::create('zh_CN');
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci';
        }

        if (Yii::$app->db->schema->getTableSchema('{{%user}}') !== null) {
            $this->dropTable('{{%user_profile}}');
            $this->dropTable('{{%user}}');
            $this->dropTable('{{%access_log}}');
        }
        return true;
    }

    public function createRolePermissions() {
        $auth = \Yii::$app->get('authManager');
        $auth->removeAll();

        $user = $auth->createRole(User::ROLE_USER);
        $auth->add($user);

        $manager = $auth->createRole(User::ROLE_MANAGER);
        $auth->add($manager);

        $admin = $auth->createRole(User::ROLE_ADMINISTRATOR);
        $auth->add($admin);

        $permission = $auth->createPermission('内容模块');
        //$permission->description = '文章管理';
        $auth->add($permission);

        $permission = $auth->createPermission('参数设置模块');
        //$permission->description = '网站参数设置';
        $auth->add($permission);

        $permission = $auth->createPermission('后台用户模块');
        //$permission->description = '后台用户列表';
        $auth->add($permission);

        $permission = $auth->createPermission('日志模块');
        //$permission->description = '后台用户操作日志';
        $auth->add($permission);

        $permission = $auth->createPermission('角色权限模块');
        //$permission->description = '角色权限管理';
        $auth->add($permission);

        // 定义 ROLE_ADMINISTRATOR 有哪些权限
        $auth->addChild($auth->getRole(\common\models\User::ROLE_ADMINISTRATOR), $auth->getPermission('角色权限模块'));
        $auth->addChild($auth->getRole(\common\models\User::ROLE_ADMINISTRATOR), $auth->getRole(User::ROLE_MANAGER)); // ROLE_ADMINISTRATOR 有 ROLE_MANAGER 的所有权限

        // 定义 ROLE_MANAGER 有哪些权限
        $auth->addChild($auth->getRole(\common\models\User::ROLE_MANAGER), $auth->getPermission('后台用户模块'));
        $auth->addChild($auth->getRole(\common\models\User::ROLE_MANAGER), $auth->getPermission('参数设置模块'));
        $auth->addChild($auth->getRole(\common\models\User::ROLE_MANAGER), $auth->getPermission('日志模块'));
        $auth->addChild($auth->getRole(\common\models\User::ROLE_MANAGER), $auth->getRole(User::ROLE_USER)); // ROLE_MANAGER 有 ROLE_USER 的所有权限

        // 定义 ROLE_MANAGER 有哪些权限
        $auth->addChild($auth->getRole(\common\models\User::ROLE_USER), $auth->getPermission('内容模块'));

        // 为ID为1,2,3的用户分配角色
        $auth->assign($auth->getRole(User::ROLE_ADMINISTRATOR), 1);
        $auth->assign($auth->getRole(User::ROLE_MANAGER), 2);
        $auth->assign($auth->getRole(User::ROLE_USER), 3);

        echo __METHOD__ . PHP_EOL;

        return;
    }

    public function seed()
    {
        $faker = \Faker\Factory::create('zh_CN');

        ImageManager::deleteAll();
        $files = FileHelper::findFiles(Yii::getAlias('@backend/web/image-samples/products'), ['only' => ['*.jpg']]);
        foreach ($files as $fileName) {
            $sFileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $sFileName = pathinfo($fileName, PATHINFO_BASENAME);
            $model = new ImageManager();
            $model->fileName = str_replace("_", "-", strtolower($sFileName));
            //$model->fileHash = Yii::$app->getSecurity()->generateRandomString(32);
            $model->fileHash = strtolower($sFileName);
            $model->save();
            $toFileName = Yii::$app->imagemanager->mediaPath . DIRECTORY_SEPARATOR . $model->id . '_' . $model->fileHash . '.' . $sFileExtension;
            copy($fileName, $toFileName);
        }
        $imageIds = ImageManager::find()->select('id')->column();

        $files = FileHelper::findFiles(Yii::getAlias('@backend/web/image-samples/people'), ['only' => ['*.jpg']]);
        $tmpAvatars = [];
        foreach ($files as $fileName) {
            $sFileName = pathinfo($fileName, PATHINFO_BASENAME);
            $tmpAvatars[] = [
                'path' => $sFileName,
                'name' => $sFileName,
                'size' => filesize($fileName),
                'type' => 'image/jpeg',
                'order' => '',
                'base_url' => Yii::getAlias('@backendUrl/image-samples/people'),
            ];
        }

        for ($i = 0; $i < 21; $i++) {
            $model = new \common\models\Member();
            $model->setAttributes([
                'username' => $faker->word . rand(0, 1000),
                'name' => $faker->name,
                'mobile' => $i == 0 ? '13900000001' : ($i == 1 ? '13800000001' : $faker->phoneNumber),
                'status' => rand(Member::STATUS_ACTIVE, Member::STATUS_ACTIVE),
                'picture' => $tmpAvatars[array_rand($tmpAvatars)],
            ], true);
            $model->setPassword('123456');
            echo "\n insert Member " . ($model->save() ? 'ok' : 'err' . print_r($model->errors, true)) . "\n";
        }

    }

}
