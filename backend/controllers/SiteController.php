<?php
namespace backend\controllers;

use common\helpers\Tools;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    // 总体框架
    public function actionIndex()
    {
        // 生成权限菜单
        $roleId = \Yii::$app->session->get('role_id'); //得到角色的等级
        $menu = Tools::makeMenu($roleId); //制作相应的菜单
        //

        return $this->render('/main', [
            'menu' => $menu
        ]);
    }

    // 后台首页
    public function actionFirst()
    {
        return $this->render('first');
    }
}
