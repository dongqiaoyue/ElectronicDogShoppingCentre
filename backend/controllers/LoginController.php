<?php
namespace backend\controllers;

use common\helpers\Tools;
use common\helpers\ValidateCode;
use common\models\Verifycodeinfo;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Session;

class LoginController extends Controller
{
    private $captcha_name = 'validate_code';
    // 登录系统
    public function actionIndex()
    {
        $this->layout = false; // 禁用布局

        return $this->render('index');
    }

    /**
     * 获取验证码
     */
    public function actionImg_captcha(){
        $font_path = \Yii::$app->getBasePath().'/web/fonts/actionj.ttf';
        $captcha_handle = new ValidateCode( $font_path );
        $captcha_handle->doimg();

        $infos = Verifycodeinfo::find()->where(['phone' => 'verifyCode'])->all();
        foreach ($infos as $info){
            $info->delete();
        }

        $verifyCode = new Verifycodeinfo();
        $Code = $captcha_handle->getCode();
        $verifyCode->id = Tools::create_id();
        $verifyCode->verifyCode = $Code;
        $verifyCode->addAt = time();
        $verifyCode->phone = 'verifyCode';
        $verifyCode->save();

    }

    // 处理登录
    public function actionDoLogin()
    {
        $request = \Yii::$app->request;
        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();

            if(empty($param['username'])){
                return ['code' => -1, 'data' => '', 'msg' => '请输入用户名'];
            }

            if(empty($param['password'])){
                return ['code' => -2, 'data' => '', 'msg' => '请输入密码'];
            }

            if(empty($param['verifyCode'])){
                return ['code' => -2, 'data' => '', 'msg' => '请输入验证码'];
            }

            //获取当前验证码
            $verifyCode = Verifycodeinfo::find()->where(['verifyCode' => $param['verifyCode'], 'phone' => 'verifyCode'])->one();
            if(!$verifyCode){
                return ['code' => -6, 'data' => '', 'msg' => '验证码错误'];
            }else{
                $verifyCode->delete();
            }


            $has = (new Query())->from('pay_admins')->where(['admin_name' => $param['username']])->one();
            if(empty($has)){
                return ['code' => -3, 'data' => '', 'msg' => '管理员不存在'];
            }

            if(2 == $has['status']){
                return ['code' => -4, 'data' => '', 'msg' => '您已经被禁用'];
            }

            if(md5($param['password'] . \Yii::$app->params['salt']) != $has['password']){
                return ['code' => -5, 'data' => '', 'msg' => '用户名密码错误'];
            }

            $session = \Yii::$app->session;
            $session->set('admin_name', $has['admin_name']);
            $session->set('admin_id', $has['admin_id']);
            $session->set('role_id', $has['role_id']);

            return ['code' => 1, 'data' => '/site/index', 'msg' => '登录成功'];
        }
    }

    // 退出登录
    public function actionLoginOut()
    {
        $session = \Yii::$app->session;
        $session->remove('admin_name');
        $session->remove('admin_id');

        \Yii::$app->response->redirect('/site/index', 301)->send();
    }
}
