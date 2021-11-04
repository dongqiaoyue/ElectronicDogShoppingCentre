<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2019/7/24
 * Time: 13:22
 */
namespace backend\controllers;

use common\helpers\Tools;
use backend\models\Area;
use yii\web\Response;

class AreaController extends BaseController
{
    public function actionIndex()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = 'Pid = 0 and Id <> 0';

            if (!empty($param['searchText'])) {
                $where = ['like', 'Name', $param['searchText']];
            }

            $selectResult = Area::getAreasByWhere($where, $offset, $limit);

            // 拼装参数
            foreach($selectResult as $key => $vo){
                //还原时间格式
                $add_time = $selectResult[$key]['addAt'];
                $selectResult[$key]['addAt'] = date("Y-m-d H:i:s", $add_time);

                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['Id'],1));
            }

            $return['total'] = Area::getAreaNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }

        return $this->render('index');
    }

    public function actionBelow()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();
            //var_dump($param['id']);
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = ['Pid' => $param['id']];

            if (!empty($param['searchText'])) {
                $where = ['like', 'Name', $param['searchText']];
            }

            //如果是直辖市没有下级地区
            if($param['id'] == '110000' || $param['id'] == '120000' || $param['id'] == '310000' || $param['id'] == '500000'){
                $is_parent = 0;
            } elseif(substr($param['id'],2,4) == '0000'){ //父级是省
                $is_parent = 1;
            } else {
                $is_parent = 0;
            }

            $selectResult = Area::getAreasByWhere($where, $offset, $limit);

            // 拼装参数
            foreach($selectResult as $key => $vo){
                //还原时间格式
                $add_time = $selectResult[$key]['addAt'];
                $selectResult[$key]['addAt'] = date("Y-m-d H:i:s", $add_time);

                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['Id'],$is_parent));
            }

            $return['total'] = Area::getAreaNum($where);  // 总数据
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
     * 添加地区
     * @return array
     */
    public function actionAdd()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();


            $area = new Area();
            $res = $area->addArea($param);

            return $res;
        }

        $parentID = $request->get()['parentID'];
        return $this->render('add',[
            'parentID' => $parentID
        ]);
    }

    /**
     * 删除地区
     * @return array
     */
    public function actionDel()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $area = new Area();
            $res = $area->delArea($id);

            return $res;
        }
    }


    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeButton($id,$is_parent)
    {
        if($is_parent == 1)//有下级
        {
            return [
                '下级地区' => [
                    'auth' => 'area/below',
                    'href' => "javascript:areaBelow(" . $id . ")",
                    'btnStyle' => '',
                    'icon' => 'fa fa-sort-amount-desc'
                ],
                '删除' => [
                    'auth' => 'area/del',
                    'href' => "javascript:areaDel(&quot;" . $id . "&quot;)",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ]
            ];
        } else {
            return [
                '删除' => [
                    'auth' => 'area/del',
                    'href' => "javascript:areaDel(&quot;" . $id . "&quot;)",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ]
            ];
        }


    }

}