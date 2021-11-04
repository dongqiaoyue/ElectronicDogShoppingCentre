<?php
namespace backend\controllers;

use backend\models\Nodes;
use backend\models\Roles;
use common\helpers\Tools;
use yii\db\Query;
use yii\web\Response;

class RolesController extends BaseController
{
    // 角色管理
    public function actionIndex()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];

            if (!empty($param['searchText'])) {
                $where = ['like', 'role_name', $param['searchText']];
            }

            $selectResult = Roles::getRolesByWhere($where, $offset, $limit);

            // 拼装参数
            foreach($selectResult as $key => $vo){

                // 状态
                $selectResult[$key]['status'] = '<span class="label label-success">启用</span>';
                if(2 == $vo['status']){

                    $selectResult[$key]['status'] = '<span class="label label-warning">禁用</span>';
                }

                if(1 != $vo['role_id']){
                    $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['role_id']));
                }

            }

            $return['total'] = Roles::getRolesNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }

        return $this->render('index');
    }

    // 添加角色
    public function actionAdd()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();

            $roles = new Roles();
            $res = $roles->addRoles($param);

            return $res;
        }

        return $this->render('add', [
            'status' => Roles::getStatus()
        ]);
    }

    // 编辑角色
    public function actionEdit()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();

            $roles = new Roles();
            $res = $roles->editRoles($param);

            return $res;
        }

        return $this->render('edit', [
            'status' => Roles::getStatus(),
            'info' => Roles::getRoleById($request->get('id'))
        ]);
    }

    // 删除角色
    public function actionDel()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $nodes = new Roles();
            $res = $nodes->delRole($id);

            return $res;
        }
    }

    // 分配权限
    public function actionAllot()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;

            $param = $request->post();

            if(isset($param['rules']) && !empty($param['rules'])){

                // 存储角色权限节点
                $rules = (new Query())->from('rbac_nodes')->where(['ID' => $param['rules']])->all();

                $finalRules = []; // 权限节点
                $menu = []; // 菜单节点

                foreach($rules as $key => $vo){
                    $finalRules[] = $vo['auth_rule'];
                    if(2 == $vo['is_menu']){
                        $menu[] = $vo;
                    }
                }

                \Yii::$app->cache->set('role_' . $param['role_id'] . '_auth', serialize($finalRules));
                \Yii::$app->cache->set('role_' . $param['role_id'] . '_menu', serialize($menu));

                $param['rules'] = implode(',', $param['rules']);
            }else{

                $param['rules'] = '';
            }

            $role = new Roles();
            $res = $role->updateRules($param);

            return $res;
        }

        // 全部的节点
        $nodes = Nodes::getAllNodes();
        $nodes = Tools::getTree($nodes);

        $roleId = $request->get('id');
        $info = explode(',', Roles::getRoleById($roleId)['rule']);

        return $this->render('allot', [
            'nodes' => $nodes,
            'role_id' => $roleId,
            'info' => $info
        ]);
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
                'auth' => 'roles/edit',
                'href' => "javascript:roleEdit(" . $id . ")",
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],
            '删除' => [
                'auth' => 'roles/del',
                'href' => "javascript:roleDel(" . $id . ")",
                'btnStyle' => 'danger',
                'icon' => 'fa fa-trash-o'
            ],
            '权限分配' => [
                'auth' => 'roles/allot',
                'href' => "javascript:roleAllot(" . $id . ")",
                'btnStyle' => 'success',
                'icon' => 'fa fa-briefcase'
            ]
        ];
    }
}
