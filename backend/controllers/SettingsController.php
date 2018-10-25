<?php
namespace backend\controllers;

use hbhe\settings\models\FormModel;
use Yii;

/**
 * Settings controller
 */
class SettingsController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function actionIndex()
    {
        $model = new FormModel([
            'keyStorage' => 'ks',
            'keys' => [
                'frontend.maintenance' => [
                    'label' => Yii::t('backend', 'Frontend maintenance mode'),
                    'type' => FormModel::TYPE_DROPDOWN,
                    'items' => [
                        'disabled' => Yii::t('backend', 'Disabled'),
                        'enabled' => Yii::t('backend', 'Enabled')
                    ]
                ],
                'backend.theme-skin' => [
                    'label' => Yii::t('backend', 'Backend theme'),
                    'type' => FormModel::TYPE_DROPDOWN,
                    'items' => [
                        'skin-black' => 'skin-black',
                        'skin-blue' => 'skin-blue',
                        'skin-green' => 'skin-green',
                        'skin-purple' => 'skin-purple',
                        'skin-red' => 'skin-red',
                        'skin-yellow' => 'skin-yellow'
                    ]
                ],
                'backend.layout-fixed' => [
                    'label' => Yii::t('backend', 'Fixed backend layout'),
                    'type' => FormModel::TYPE_CHECKBOX
                ],
                'backend.layout-boxed' => [
                    'label' => Yii::t('backend', 'Boxed backend layout'),
                    'type' => FormModel::TYPE_CHECKBOX
                ],
                'backend.layout-collapsed-sidebar' => [
                    'label' => Yii::t('backend', 'Backend sidebar collapsed'),
                    'type' => FormModel::TYPE_CHECKBOX
                ]
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '保存成功!');
            return $this->refresh();
        }

        return $this->render('index', [
            'title' => '主题设置',
            'model' => $model
        ]);
    }

    /*
     * Yii::$app->ks->get('demo.number', 0)
     */
    public function actionDemo()
    {
        $model = new FormModel([
            'keyStorage' => 'ks',
            'keys' => [
                'demo.text' => [
                    'label' => '文本框',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['string', 'min' => 1, 'max' => 8]],
                ],
                'demo.number' => [
                    'label' => '数字框(1~100)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 10],
                    'rules' => [['number', 'min' => 1, 'max' => 100]],
                ],
                'demo.dropdown' => [
                    'label' => '下拉框',
                    'type' => FormModel::TYPE_DROPDOWN,
                    'items' => [
                        'AAAA' => 'AAAA',
                        'BBBB' => 'BBBB',
                    ]
                ],
                'demo.check' => [
                    'label' => '勾选框',
                    'type' => FormModel::TYPE_CHECKBOX
                ],
                'demo.date' => [
                    'label' => '日期(格式如2019-01-01 01:01:01)',
                    'type' => FormModel::TYPE_TEXTINPUT,
                    'options' => ['maxlength' => 24,],
                    'rules' => [['date', 'format' => 'php:Y-m-d H:i:s']],
                ],
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '保存成功!');
            return $this->refresh();
        }
        Yii::info([
            Yii::$app->ks->get('demo.number', 0),
        ]);
        return $this->render('index', [
            'title' => 'DEMO参数设置',
            'model' => $model
        ]);
    }
}
