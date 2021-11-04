<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2019/7/24
 * Time: 14:50
 */
namespace backend\controllers;

use backend\models\Roles;
use common\helpers\Tools;
use backend\models\Dictionary;
use yii\web\Response;


class DictionaryController extends BaseController
{
    public function actionIndex()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = ['=', 'parentID', '0'];

            if (!empty($param['searchText'])) {
                $where = ['like', 'Name', $param['searchText']];
            }

            $selectResult = Dictionary::getDictsByWhere($where, $offset, $limit);

            // 拼装参数
            foreach($selectResult as $key => $vo){
                //还原时间格式
                $add_time = $selectResult[$key]['addAt'];
                $selectResult[$key]['addAt'] = date("Y-m-d H:i:s", $add_time);

                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['ID'], '1'));
            }

            $return['total'] = Dictionary::getDictsNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }

        return $this->render('index');
    }

    public function actionAdd()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();


            $dictionary = new Dictionary();
            $res = $dictionary->addDicts($param);

            return $res;
        }

        // 全部的节点
        $dicts = Dictionary::getAlldicts();
        $dicts = Tools::getTree($dicts);

        return $this->render('add', [
            'dicts' => $dicts // 全部的角色
        ]);
    }

    // 编辑节点
    public function actionEdit()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();

            $dictionary = new Dictionary();
            $res = $dictionary->editDicts($param);

            return $res;
        }
        // 全部的节点
        $dicts = Dictionary::getAlldicts();
        $dicts = Tools::getTree($dicts);
        //取出 id 值
        $id = $request->get('id');

        return $this->render('edit', [
            'dicts' => $dicts,
            'info' => Dictionary::getDictById($id)
        ]);
    }

    // 删除节点
    public function actionDel()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $dictionary = new Dictionary();
            $res = $dictionary->delDict($id);

            return $res;
        }
    }

    public function actionBelow()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];


            $where = ['=', 'parentID' , $param['id']];
            if (!empty($param['searchText'])) {
                $where = ['and', ['=', 'parentID', $param['id']], ['like', 'Name', $param['searchText']]];
            }

            $selectResult = Dictionary::getDictsByWhere($where, $offset, $limit);

            // 拼装参数
            foreach($selectResult as $key => $vo){
                //还原时间格式
                $add_time = $selectResult[$key]['addAt'];
                $selectResult[$key]['addAt'] = date("Y-m-d H:i:s", $add_time);

                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['ID'], '2'));
            }

            $return['total'] = Dictionary::getDictsNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }

        return $this->render('below');
    }

    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeButton($id, $choose)
    {
        if($choose == '1'){
            $button = [
                '下级字典' => [
                    'auth' => 'dictionary/below',
                    'href' => "javascript:dictBelow(&quot;" . $id . "&quot;);",
                    'btnStyle' => '',
                    'icon' => 'fa fa-sort-amount-desc'
                ],
                '编辑' => [
                    'auth' => 'dictionary/edit',
                    'href' => "javascript:dictEdit(&quot;" . $id . "&quot;);",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
                '删除' => [
                    'auth' => 'dictionary/del',
                    'href' => "javascript:dictDel(&quot;" . $id . "&quot;);",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ]
            ];
        } else {
            $button = [
                '编辑' => [
                    'auth' => 'dictionary/edit',
                    'href' => "javascript:dictEdit(&quot;" . $id . "&quot;);",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
                '删除' => [
                    'auth' => 'dictionary/del',
                    'href' => "javascript:dictDel(&quot;" . $id . "&quot;);",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ]
            ];
        }

        return $button;
    }
}