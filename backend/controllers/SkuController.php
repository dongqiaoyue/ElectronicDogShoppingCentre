<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2019/7/26
 * Time: 12:56
 */
namespace backend\controllers;

use backend\models\Dictionary;
use backend\models\Goods;
use backend\models\Skubasic;
use backend\models\Skuingoods;
use common\helpers\Tools;
use yii\web\Response;

class SkuController extends BaseController
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

            $selectResult = Skubasic::getSkusByWhere($where, $offset, $limit);

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

    // 添加sku
    public function actionAdd()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();


            $sku = new Skubasic();
            $res = $sku->addSku($param);

            return $res;
        }

        // 全部的节点
        $skus = Skubasic::getAllSku();
        $skus = Tools::getTree($skus);

        return $this->render('add', [
            'skus' => $skus // 全部的角色
        ]);
    }

    // 编辑管理员
    public function actionEdit()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();

            $sku = new Skubasic();
            $res = $sku->editSkus($param);

            return $res;
        }
        // 全部的节点
        $skus = Skubasic::getAllSku();
        $skus = Tools::getTree($skus);
        //取出 id 值
        $id = $request->get('id');

        return $this->render('edit', [
            'skus' => $skus,
            'info' => Skubasic::getSkuById($id)
        ]);
    }

    // 删除管理员
    public function actionDel()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $sku = new Skubasic();
            $res = $sku->delSku($id);

            return $res;
        }
    }

//    /**
//     * 封面上传 成功则返回一个存储路径
//     * @return string
//     */
//    public function actionCov()
//    {
//        $request = \Yii::$app->request;
//
//        if($request->isPost){
//            // 检测节点名称的唯一性
//            $has = Skuingoods::find()->select(['id'])->where(['cover' => $_FILES['file']['name']])->one();
//
//            if(!empty($has)){
//                $res =  ['code' => -2, 'data' => '', 'msg' => '该图片已经上传或同名'];
//            }else{
//                //获取web根目录
//                $docroot = "/upload/cover/";
//                $res = Tools::file_upload($docroot);
//            }
//
//            return Tools::json($res);
//        }
//    }


    //批量删除
    public function actionDelSelected()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $ids = $request->get('ids');

            $sku = new Skubasic();
            $res = $sku->delSkuSelected($ids);

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

            $selectResult = Skubasic::getSkusByWhere($where, $offset, $limit);

            // 拼装参数
            foreach($selectResult as $key => $vo){
                //还原时间格式
                $add_time = $selectResult[$key]['addAt'];
                $selectResult[$key]['addAt'] = date("Y-m-d H:i:s", $add_time);

                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['ID'], '2'));
            }

            $return['total'] = Skubasic::getSkusNum($where);  // 总数据
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
                    'auth' => 'sku/below',
                    'href' => "javascript:skuBelow(&quot;" . $id . "&quot;);",
                    'btnStyle' => '',
                    'icon' => 'fa fa-sort-amount-desc'
                ],
                '编辑' => [
                    'auth' => 'sku/edit',
                    'href' => "javascript:skuEdit(&quot;" . $id . "&quot;);",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
                '删除' => [
                    'auth' => 'sku/del',
                    'href' => "javascript:skuDel(&quot;" . $id . "&quot;);",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ]
            ];
        } else {
            $button = [
                '编辑' => [
                    'auth' => 'sku/edit',
                    'href' => "javascript:skuEdit(&quot;" . $id . "&quot;);",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
                '删除' => [
                    'auth' => 'sku/del',
                    'href' => "javascript:skuDel(&quot;" . $id . "&quot;);",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ]
            ];
        }

        return $button;
    }
}