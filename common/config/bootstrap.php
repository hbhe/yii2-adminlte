<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@storage', dirname(dirname(__DIR__)) . '/storage');
Yii::setAlias('@rest', dirname(dirname(__DIR__)) . '/rest');
Yii::setAlias('@wap', dirname(dirname(__DIR__)) . '/wap');

Yii::setAlias('@frontendUrl', YII_ENV_DEV ? 'http://127.0.0.1/yii2-adminlte/frontend/web/' : 'http://admin.site.com');
Yii::setAlias('@backendUrl', YII_ENV_DEV ? 'http://127.0.0.1/yii2-adminlte/backend/web/' : 'http://www.site.com');
Yii::setAlias('@storageUrl', YII_ENV_DEV ? 'http://127.0.0.1/yii2-adminlte/storage/web/' : 'http://storage.site.com');
Yii::setAlias('@restUrl', YII_ENV_DEV ? 'http://127.0.0.1/yii2-adminlte/rest/web/' : 'http://api.site.com');
Yii::setAlias('@wapUrl', YII_ENV_DEV ? 'http://127.0.0.1/yii2-adminlte/wap/web/' : 'http://m.site.com');
