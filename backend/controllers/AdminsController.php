<?php
namespace backend\controllers;

use backend\models\Admins;
use backend\models\Roles;
use common\helpers\Tools;
use yii\web\Response;

class AdminsController extends BaseController
{
    // 节点管理
    public function actionIndex()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];

            if (!empty($param['searchText'])) {
                $where = ['like', 'admin_name', $param['searchText']];
            }

            $selectResult = Admins::getAdminsByWhere($where, $offset, $limit);

            $status = Admins::getStatus();

            // 拼装参数
            foreach($selectResult as $key => $vo){
                // 查询管理员角色
                $role = Roles::getRoleById($vo['role_id']);
                if(!empty($role)){
                    $selectResult[$key]['role_name'] = $role['role_name'];
                }

                // 状态
                isset($status[$vo['status']]) && $selectResult[$key]['status'] = $status[$vo['status']];

//                if(1 != $vo['admin_id']){
//                    $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['admin_id']));
//                }
                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['admin_id']));
            }

            $return['total'] = Admins::getAdminsNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }

        return $this->render('index');
    }

    // 添加管理员
    public function actionAdd()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();

            $param['password'] = md5($param['password'] . \Yii::$app->params['salt']);

            $admin = new Admins();
            $res = $admin->addAdmins($param);

            return $res;
        }

        return $this->render('add', [
            'roles' => Roles::getSystemRoles() // 全部的角色
        ]);
    }

    // 编辑管理员
    public function actionEdit()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();

            if(!empty($param['re_password'])){

                $param['password'] = md5($param['re_password'] . \Yii::$app->params['salt']);
                unset($param['re_password']);
            }

            $admin = new Admins();
            $res = $admin->editAdmins($param);

            return $res;
        }

        return $this->render('edit', [
            'roles' => Roles::getSystemRoles(), // 全部的角色
            'info' => Admins::getAdminById($request->get('id'))
        ]);
    }

    // 删除管理员
    public function actionDel()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $admin = new Admins();
            $res = $admin->delAdmin($id);

            return $res;
        }
    }




    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeButton($id)
    {
        return [
            '编辑' => [
                'auth' => 'admins/edit',
                'href' => "javascript:adminEdit(" . $id . ")",
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],
            '删除' => [
                'auth' => 'admins/del',
                'href' => "javascript:adminDel(" . $id . ")",
                'btnStyle' => 'danger',
                'icon' => 'fa fa-trash-o'
            ]
        ];
    }
}