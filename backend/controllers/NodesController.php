<?php
namespace backend\controllers;

use backend\models\Nodes;
use common\helpers\Tools;
use yii\web\Response;

class NodesController extends BaseController
{
    // 节点管理
    public function actionIndex()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize']; //每页项数
            $offset = ($param['pageNumber'] - 1) * $limit; //页数
            $where = ['parentID' => 0];//只显示顶级结点

            if (!empty($param['searchText'])) {
                //$where = ['like', 'Name', $param['searchText']];
                $where = ['and',['parentID' => 0],['like', 'Name', $param['searchText']]];
                //return $where;
            }

            $selectResult = Nodes::getNodesByWhere($where, $offset, $limit);

            // 拼装参数
            foreach($selectResult as $key => $vo){

                if(!empty($vo['style'])){
                    $selectResult[$key]['style'] = '<i class="' . $vo['style'] . '"></i>';
                }

                // 父层节点
                $selectResult[$key]['parent_name'] = '顶级节点';
                if($vo['parentID'] > 0){

                    $selectResult[$key]['parent_name'] = Nodes::getNodeById($vo['parentID'])['Name'];
                }

                // 菜单项
                $selectResult[$key]['is_menu'] = '<span class="label label-success">否</span>';
                if(2 == $vo['is_menu']){

                    $selectResult[$key]['is_menu'] = '<span class="label label-warning">是</span>';
                }

                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['ID'],$vo['is_menu']));
            }

            $return['total'] = Nodes::getNodesNum($where);  // 总数据
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

            $nodes = new Nodes();
            $res = $nodes->addNodes($param);

            return $res;
        }

        // 全部的节点
        $nodes = Nodes::getAllNodes();
        $nodes = Tools::getTree($nodes);

        return $this->render('add', [
            'nodes' => $nodes
        ]);
    }

    // 编辑节点
    public function actionEdit()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();

            $nodes = new Nodes();
            $res = $nodes->editNodes($param);

            return $res;
        }

        // 全部的节点
        $nodes = Nodes::getAllNodes();
        $nodes = Tools::getTree($nodes);

        return $this->render('edit', [
            'nodes' => $nodes,
            'info' => Nodes::getNodeById($request->get('id'))
        ]);
    }

    // 删除节点
    public function actionDel()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $nodes = new Nodes();
            $res = $nodes->delNode($id);

            return $res;
        }
    }

    //下级结点
    public function actionBelow()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){
            $param = $request->get();

            $limit = $param['pageSize']; //每页项数
            $offset = ($param['pageNumber'] - 1) * $limit; //页数
            $where = ['parentID' => $param['id']];

            $selectResult = Nodes::getNodesByWhere($where, $offset, $limit);

            // 拼装参数
            foreach ($selectResult as $key => $vo) {

                if (!empty($vo['style'])) {
                    $selectResult[$key]['style'] = '<i class="' . $vo['style'] . '"></i>';
                }

                // 父层节点
                $selectResult[$key]['parent_name'] = '顶级节点';
                if ($vo['parentID'] > 0) {

                    $selectResult[$key]['parent_name'] = Nodes::getNodeById($vo['parentID'])['Name'];
                }

                // 菜单项
                $selectResult[$key]['is_menu'] = '<span class="label label-success">否</span>';
                if (2 == $vo['is_menu']) {

                    $selectResult[$key]['is_menu'] = '<span class="label label-warning">是</span>';
                }

                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['ID'],$vo['is_menu']));
            }

            $return['total'] = Nodes::getNodesNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }
        $param = $request->get();
        $id = $param['id'];
        return $this->render('below',[
            'id' => $id
        ]);
    }


    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeButton($id,$is_menu)
    {
        if($is_menu == 2)
        {
            return [
                '下级节点' => [
                    'auth' => 'nodes/below',
                    'href' => "javascript:nodeBelow(" . $id . ")",
                    'btnStyle' => '',
                    'icon' => 'fa fa-sort-amount-desc'
                ],
                '编辑' => [
                    'auth' => 'nodes/edit',
                    'href' => "javascript:nodeEdit(" . $id . ")",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
                '删除' => [
                    'auth' => 'nodes/del',
                    'href' => "javascript:nodeDel(" . $id . ")",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ]
            ];
        }
        if($is_menu == 1)
        {
            return [
                '编辑' => [
                    'auth' => 'nodes/edit',
                    'href' => "javascript:nodeEdit(" . $id . ")",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
                '删除' => [
                    'auth' => 'nodes/del',
                    'href' => "javascript:nodeDel(" . $id . ")",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ]
            ];
        }

    }
}
