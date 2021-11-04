<?php
namespace backend\controllers;

use common\helpers\Tools;
use mdm\admin\models\Route;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Request;
use yii\helpers;

/**
 * Base controller
 */
class BaseController extends Controller
{
    public function init()
    {
        $this->layout = false; // 禁用布局
        $request = \Yii::$app->request;

        if(empty(\Yii::$app->session->get('admin_name'))){

            if($request->isAjax){
                \Yii::$app->response->format = Response::FORMAT_JSON;

                return ['code' => 111, 'data' => '', 'msg' => 'try to login'];
            }
            //return Url::toRoute(['/login/index']);
            \Yii::$app->response->redirect('/login/index', 301)->send();
            //\Yii::$app->response->redirect(Url::base().'/login/index', 301)->send();
            //\Yii::$app->response->redirect(helpers\BaseUrl::base().'/login/index', 301)->send();
        }

        // 权限校验
        $run = \Yii::$app->requestedRoute;
        if(false === Tools::authCheck($run)){
            echo $this->render('@backend/views/error', [
                'info' => '无权操作',
            ]);
            exit();
        }
    }
}
