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

class SkuingoodsController extends BaseController
{
    public function actionIndex()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            //$where = ['=', 'parentID', '0'];
            $where = [];
            if (!empty($param['searchText'])) {
                $where = ['like', 'Name', $param['searchText']];
            }

            $selectResult = Goods::getGoodsByWhere($where, $offset, $limit);

            foreach($selectResult as $key => $vo) {
                //价格公式
                $formula = Dictionary::find()->select('Name')->where(['Code' =>$selectResult[$key]['id']])->one();
                if(!empty($formula))
                {
                    $selectResult[$key]['formula'] = $formula->Name;
                }else{
                    $selectResult[$key]['formula'] ="";
                }

                //添加时间
                $selectResult[$key]['addAt']=date("Y-m-d H:i:s",$selectResult[$key]['addAt']);

                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['id']));
            }

            $return['total'] = Goods::getGoodNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
            }

        return $this->render('index');
    }

    // 添加优惠
    public function actionAddDiscount()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();
            $discount = new Dictionary();
            $res = $discount->addDiscount($param);
            return  $res;
        }
        $id = $request->get('id');
        return $this->render('add-discount',[
            'id' =>$id
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

    // 删除优惠
    public function actionDelDiscount()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $discount = new Dictionary();
            $res = $discount->delDiscount($id);

            return $res;
        }
    }

    //批量删除
    public function actionDelSelected()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $ids = $request->get('ids');

            $sku = new Skuingoods();
            $res = $sku->delSkuSelected($ids);

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
            '设置优惠' => [
                'auth' => 'skuingoods/addDiscount',
                'href' => "javascript:addDiscount(&quot;" . $id . "&quot;)",
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],
            '删除优惠' => [
                'auth' => 'skuingoods/delDiscount',
                'href' => "javascript:delDiscount(&quot;" . $id . "&quot;)",
                'btnStyle' => 'danger',
                'icon' => 'fa fa-trash-o'
            ]
        ];
    }
}