##本项的目的是在用Yii2开发时有一个好的起点！它采用advanced目录结构, 基于adminlte后台模板, 提供常用的模块. 

#基本功能模块：
- 后台用户管理
- 后台用户操作日志
- 网站参数设置
- 角色权限管理
- REST API配置就绪

#可选模块    
- 文章管理
- 微信公众号(TODO)
- 商城(TODO)



目录结构
-------------------


CREATE DATABASE yii2adminlte DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

## 目录结构


```
common
    config/              放置公共配置文件
    mail/                
    models/              放置前台端共用的公共model文件
    tests/               测试单元    
console
    config/              放置CLI命令行配置文件
    controllers/         CLI脚本文件
    migrations/          数据库迁移命令文件
    models/              -
    runtime/             放置执行过程中产生的文件
backend
    assets/              放置后台静态文件 JavaScript and CSS
    config/              后台配置文件
    controllers/         后台controller文件
    models/              后台相关的model文件
    runtime/             后台脚本执行过程中产生的文件，包括log文件
    tests/               测试单元    
    views/               后台view文件
    web/                 web可访问路径, 脚本入口
frontend
    assets/              放置前台静态文件 JavaScript and CSS
    config/              前台配置文件
    controllers/         前台controller文件
    models/              前台相关的model文件
    runtime/             前台脚本执行过程中产生的文件，包括log文件
    tests/               测试单元 
    views/               前台view文件
    web/                 web可访问路径, 脚本入口
    widgets/             前台挂件
rest
    config/              API 接口配置文件，主要是路由配置！
    controllers/         接口controller文件
    models/              接口相关的model文件
    modules/             前台相关的模块文件    
        v1/              版本
            controllers/ 主要的接口controller文件            
    runtime/             API过程中产生的文件，包括log文件
    web/                 接口脚本入口    
wap
    config/              H5相关配置
    controllers/         
    runtime/             
    web/                 H5脚本入口    
storage
    config/              
    controllers/         
    runtime/             
    web/                 上传图片,文件   

vendor/                  第三方包依赖包文件
environments/            环境文件
```


## 安装步骤

1. 克隆代码到本地目录如yii2-adminlte
```
cd yii2-adminlte
git clone https://github/hbhe/yii2-adminlte.git
composer install
```

2. 检查PHP环境是否满足系统要求
```
cd yii2-adminlte
php requirements.php
```

3. 创建数据库, 如
```
CREATE DATABASE yii2adminlte DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

4. 初始化环境
```
php init
```

5. 配置本地main-local参数, 主要是数据库参数
```
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2adminlte',
            'username' => 'root',
            'password' => '',
            'tablePrefix' => 'ya_',
            'charset' => 'utf8',
            'enableSchemaCache' => !YII_DEBUG,
        ],
    ]
```
5. 执行数据库初始化脚本
```
php yii migrate --migrationPath=@yii/rbac/migrations                        // yii migrate/down 3 --migrationPath=@yii/rbac/migrations          
php yii migrate --migrationPath=@mdm/admin/migrations                       // yii migrate/down 2  --migrationPath=@mdm/admin/migrations
php yii migrate --migrationPath=@noam148/imagemanager/migrations            // yii migrate/down 2 --migrationPath=@noam148/imagemanager/migrations
php yii migrate/up --migrationPath=@common/modules/content/migrations 
php yii migrate/up --migrationPath=@hbhe/settings/migrations
php yii migrate/up
```

**enjoy it :)**

如果是nginx, 使用api时需要美化URL, 必须配置一下nginx.conf， 加上
location / {
    try_files $uri $uri/ /index.php?$args;
}

Note: This project is inspired by yii2-starter-kit, I just need a light and clean advanced-app with adminlte template.

图片域名: yii2-adminlte-storage.mitoto.cn

API域名: yii2-adminlte-api.mitoto.cn


###############

DEMO演示地址:  http://yii2-adminlte-backend.mitoto.cn

账号： webmaster

密码: webmaster

QQ交流群:  342271822
