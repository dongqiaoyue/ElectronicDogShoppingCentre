<?php

namespace backend\controllers;

use backend\models\Dictionary;
use backend\models\Goods;
use backend\models\Goodshistory;
use backend\models\Skuingoods;
use backend\models\Skubasic;
use backend\models\Goodsattach;
use common\helpers\Tools;
use yii\base\Controller;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use backend\models\Model;
use Yii;


class GoodsController extends BaseController
{
    public function actionIndex()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            //上下架
            if (!empty($param['searchText'])) {
                $where = ['like', 'title', $param['searchText'],];
            }elseif($param['checkStatus']!="#") {
                $where = ['like', 'status', $param['checkStatus']];
            } else {
                $where = ['like', 'title', $param['searchText'],];
            }

            $selectResult = Goods::getGoodsByWhere($where, $offset, $limit);
            $status = Goods::getStatus();
            // 拼装参数
            foreach($selectResult as $key => $vo){

                // 状态
                isset($status[$vo['status']]) && $selectResult[$key]['status'] = $status[$vo['status']];
                //添加时间
                $selectResult[$key]['addAt']=date("Y-m-d H:i:s",$selectResult[$key]['addAt']);
                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['id'],$vo['status']));
//                //版本
//                $version = Dictionary::find()->where(['Code' => $selectResult[$key]['ver']])->one();
//                $selectResult[$key]['version'] = $version->Name ;

            }

            $return['total'] = Goods::getGoodNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }

        return $this->render('index');
    }
    //添加商品
    public function actionAdd()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();
            $goods = new Goods();//存名称,简介,备注
            $param['goodsID'] = Tools::create_id();
            $goods->id =  $param['goodsID'];
            $goodsRes = $goods->addGoods($param);
            if($goodsRes['code'] != 1){
                return $goodsRes;
            }

            $goodsHist = new Goodshistory();
            $histtoryRes = $goodsHist->addGoodHistory($param);
            if($histtoryRes['code'] != 1){
                return $histtoryRes;
            }

            $goodsImg = new Goodsattach();
            $imgRes = $goodsImg->addAttach($param,0);
            if($imgRes['code'] != 1){
                return $imgRes;
            }

            $goodsVid = new Goodsattach();
            $vidRes = $goodsImg->addAttach($param,1);
            if($vidRes['code'] != 1){
                return $vidRes;
            }

            foreach ($param['skuingoods'] as $key => $value){
                $skuingoods = new Skuingoods();
                $skuRes = $skuingoods->addGoodSku($value);
                if($skuRes['code'] != 1){
                    return $skuRes;
                }
            }
            return $goodsRes;

        }

        return $this->render('add');
    }


    /**
     * Creates a new Customer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $goods = new Goods();
        $goodsHist = new Goodshistory();
        $goodsImg = new Goodsattach();
        $goodsVid = new Goodsattach();
        $skuGoods = [new Skuingoods()];

        $request = Yii::$app->request;

        if($request->isPost) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            //判断文件是否上传成功
            $up = $this->actionUpload();
            //判断是否上传成功
            if(isset($up['path'])) {
                // id 赋值
                $goods_id = Tools::create_id();
                $goods->id = $goods_id;
                //新增版本 1
                $goods->ver = '1';
                $goods->status = '1';
                $goods->addAt = time();
                $goods->addBy = $goods['id'];
                $goods->addIP = Tools::getClientIp();
                $goods->addAgent = Tools::browse_info();

                if ($goods->load($request->post()) && $goods->validate()) {
                    //处理skuInGoods模型
                    $skuGoods = Model::createMultiple(Skuingoods::classname()); //创建多个模型
                    Model::loadMultiple($skuGoods, $request->post()); //给多个模型加载数据
//                    foreach ($skuGoods as $value => $key) {
                    for($i=0; $i<count($skuGoods); $i++){
                        //将sku图片路径赋值给相应字段
                        $skuGoods[$i]->images = $up['path'][$i];
                        //赋值id
                        $skuGoods[$i]->goodsID = $goods_id;
                        $skuGoods[$i]->id = Tools::create_id();
                        //新增版本 1
                        $skuGoods[$i]->ver = '1';
                    }
//                    }
                    //验证goodsimg是否上传
//                    if($_POST['Goodsattach']['img']['url'] == '图片url')
//                        return ['code' => -1, 'data' => '', 'msg' => '商品图片未上传'];
                    $goodsImg->goodsID = $goods_id;
                    $goodsImg->id = Tools::create_id();
                    $goodsImg->type = '0';
                    //新增版本 1
                    $goodsImg->ver = '1';
                    if ($goodsImg->load($request->post()) && $goodsImg->validate()) {
                        //验证goodsvid
                        $goodsVid->goodsID = $goods_id;
                        $goodsVid->id = Tools::create_id();
                        $goodsVid->type = '1';
                        //新增版本 1
                        $goodsVid->ver = '1';
                        if ($goodsVid->load($request->post()) && $goodsVid->validate()) {
                            //验证skuInGoods表单模型
                            $valid = Model::validateMultiple($skuGoods);
                            if ($valid) {
                                $transaction = \Yii::$app->db->beginTransaction();
                                try {
                                    //保存商品表
                                    if ($flag = $goods->save(false)) {
                                        //保存数据到goodsHistory
                                        $goodsHist->id = $goods->id;
                                        $goodsHist->ver = $goods->ver;
                                        $goodsHist->title = $goods->title;
                                        $goodsHist->content = $goods->content;
                                        $goodsHist->status = $goods->status;
                                        $goodsHist->memo = $goods->memo;
                                        $goodsHist->addAt = $goods->addAt;
                                        $goodsHist->addIP = $goods->addIP;
                                        $goodsHist->addBy = $goods->addBy;
                                        $goodsHist->addAgent = $goods->addAgent;
                                        if($flag = $goodsHist->save(false)) {
                                            //保存视频文件
                                            if ($flag = $goodsVid->save(false)) {
                                                //保存多图
                                                if ($flag = $goodsImg->save(false)) {
                                                    //遍历保存sku信息
                                                    foreach ($skuGoods as $skuGood) {
                                                        if (($flag = $skuGood->save(false)) === false) {
                                                            //如果失败则回滚
                                                            $transaction->rollBack();
                                                            return ['code' => -3, 'data' => '', 'msg' => array_values($skuGood->errors)['0']['0']];
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if ($flag) {
                                        $transaction->commit();
                                        return ['code' => 1, 'data' => '', 'msg' => '上传文件成功'];
                                    }
                                } catch (Exception $e) {
                                    $transaction->rollBack();
                                    return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
                                }
                            }
                        }
                    }
                }
            }else{
                return $up;
            }
        }
        //取出基础sku信息
        $where = ['!=', 'parentID', 0];
        $skuBasic = Skubasic::getSkusBelow($where);

        return $this->render('create', [
            'goods' => $goods,
            'skuBasic' => $skuBasic,
            'goodsImg' => $goodsImg,
            'goodsVid' => $goodsVid,
            'skuGoods' => (empty($skuGoods)) ? [new Skuingoods()] : $skuGoods
        ]);
    }

    /**
     * 商品多文件上传
     */
    public function actionUploads()
    {
        $request = \Yii::$app->request;

        if($request->isPost){
            // 检测节点名称的唯一性
            $has = Skuingoods::find()->select(['id'])->where(['images' => $_FILES['Skuingoods']['name']])->one();

            if(!empty($has)){
                $res =  ['code' => -2, 'data' => '', 'msg' => '该图片已经上传或同名'];
            }else{
                //获取web根目录
                $docroot = $_FILES['Skuingoods'];
                $img = 'memo';
                $res = Tools::file_upload_more($docroot, $img);
            }

            return Tools::json($res);
        }
    }

    /**
     * sku多文件上传
     */
    private function actionUpload()
    {
        $request = \Yii::$app->request;

        if($request->isPost){
            // 检测节点名称的唯一性
            $has = Skuingoods::find()->select(['id'])->where(['images' => $_FILES['Skuingoods']['name']])->one();

            if(!empty($has)){
                $res =  ['code' => -2, 'data' => '', 'msg' => '该图片已经上传或同名'];
            }else{
                //获取web根目录
                $docroot = $_FILES['Skuingoods'];
                $img = 'memo';
                $res = Tools::file_upload_more($docroot, $img);
            }

            return $res;
        }
    }

    // 编辑商品信息
    public function actionEdit()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id');

        $goods = Goods::findOne($id);
        $skuGoods = Skuingoods::findAll(['goodsID' => $id]);
        $goodsImg = Goodsattach::findAll(['goodsID' => $id, 'type' => '0']);
        $goodsVid = Goodsattach::findAll(['goodsID' => $id, 'type' => '1']);


        if ($request->isPost) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();

            $res = $goods->editGoods($param['Goods']['id']);

            //$data=[];
            $skus = Skuingoods::find()->where(['goodsID' => $param['Goods']['id']])->all();
            $skuCount = Skuingoods::find()->where(['goodsID' => $param['Goods']['id']])->count();
            $count=0;
            foreach ($param['Skuingoods'] as $key => $value){
                //$skus[$key]->ver = $param['ver'];
                $skus[$key]->ver += 1;
                //$data[$key]['ver'] = $param['ver'];
                $skus[$key]->inventory = $param['Skuingoods'][$key]['inventory'];
                $skus[$key]->price = $param['Skuingoods'][$key]['price'];
//                $skus[$key]->content = $param['Skuingoods'][$key]['content'];
                $skus[$key]->updateAt = time();
                $sku = new Skuingoods();
                $skuRes = $sku->editGoodSku($skus[$key]);
                if($skus[$key]->save()){
                    $count++;
                }
//                return $skuRes;
            }
            if($res['code']==1 && $count==$skuCount){
                return $res;
                //return ['code' => 1, 'data' =>  $param, 'msg' => '编辑商品信息成功'];
            }else{
                return ['code' => -1, 'data' => $count,$skuCount, 'msg' => '编辑商品信息失败'];
            }
        }

        //取出基础sku信息
        $where = ['!=', 'parentID', 0];
        $skuBasic = Skubasic::getSkusBelow($where);

        return $this->render('edit', [
            'goods' => $goods,
            'skuBasic' => $skuBasic,
            'goodsImg' => $goodsImg,
            'goodsVid' => (empty($goodsVid)) ? [new Goodsattach()] : $goodsVid,
            'skuGoods' => (empty($skuGoods)) ? [new Skuingoods()] : $skuGoods
        ]);
    }

    // 显示商品sku
    public function actionSkuDisplay()
    {
        $request = \Yii::$app->request;

        if($request->isGet){
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $type = $request->get('type');
            if($type=="color")
            {
                $basic = Skubasic::find()->where(['name' => '颜色'])->one();
                $basics = Skubasic::find()->where(['parentID' => $basic->id])->all();
                $colors = ArrayHelper::getColumn($basics,'name');
                return ['code' => 1, 'data' => $colors, 'msg' => ''];
            }
        }
    }

    //商品信息详情
    public function actionDet()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id');
        //查询响应表的信息
        $goods = Goods::findOne($id);
//        $skuGoods = Skuingoods::findAll(['goodsID' => $id]);
//        $goodsImg = Goodsattach::findAll(['goodsID' => $id, 'type' => '0']);
//        $goodsVid = Goodsattach::findAll(['goodsID' => $id, 'type' => '1']);
        //$skuGoods = Skuingoods::findAll(['goodsID' => $id,'ver' => $goods['ver']]);
        $skuGoods = Skuingoods::find()->where(['goodsID' => $id,'ver' => $goods['ver']])->asArray()->all();
        //要颜色
        foreach ($skuGoods as $key => $value){
            $basic = (new Query())
                ->select(['Name'])
                ->from('skubasic')
                ->where(['id' => $value['skuID']])
                ->one();
            $skuGoods[$key]['color'] = $basic['Name'];
        }

        $goodsImg = Goodsattach::findAll(['goodsID' => $id, 'type' => '0','ver' => $goods['ver']]);
        $goodsVid = Goodsattach::findAll(['goodsID' => $id, 'type' => '1','ver' => $goods['ver']]);

        //取出基础sku信息
        $where = ['!=', 'parentID', 0];
        $skuBasic = Skubasic::getSkusBelow($where);

        return $this->render('det', [
            'goods' => $goods,
            'skuBasic' => $skuBasic,
            'goodsImg' => $goodsImg,
            'goodsVid' => (empty($goodsVid)) ? [new Goodsattach()] : $goodsVid,
            'skuGoods' => (empty($skuGoods)) ? [new Skuingoods()] : $skuGoods
        ]);
    }

    // 删除单个商品信息
    public function actionDel()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $agent = new Goods();
            $res = $agent->delGood($id);

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

            $agent = new Goods();
            $res = $agent->delGoodSelected($ids);

            return $res;
        }
    }

    // 上/下架
    public function actionCheck()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $res = Goods::checkGood($id);
            return $res;

        }
    }

    //代理商折扣
    public function actionDiscount()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            //$where = ['=', 'parentID', '0'];
            $where = [];
            if (!empty($param['searchText'])) {
                $where = ['like', 'title', $param['searchText']];
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

                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButtonDiscount($vo['id']));
            }

            $return['total'] = Goods::getGoodNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }

        return $this->render('discount');
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
        //名称
        $good = Goods::findOne($id);
        $title = $good->title;

        //优惠
        $dictionary = Dictionary::find()->where(['code' => $id])->one();
        if(!empty($dictionary->Name))
        {
            $discount = $dictionary->Name;
        }else{
            $discount = "";
        }


        return $this->render('add-discount',[
            'id' =>$id,
            'title' => $title,
            'discount' => $discount
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
    //批量删除优惠
    public function actionDelDiscountSelected()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $ids = $request->get('ids');

            $agent = new Dictionary();
            $res = $agent->delDiscountSelected($ids);

            return $res;
        }
    }

    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeButtonDiscount($id)
    {
        return [
            '设置优惠' => [
                'auth' => 'goods/addDiscount',
                'href' => "javascript:addDiscount(&quot;" . $id . "&quot;)",
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],
            '删除优惠' => [
                'auth' => 'goods/delDiscount',
                'href' => "javascript:delDiscount(&quot;" . $id . "&quot;)",
                'btnStyle' => 'danger',
                'icon' => 'fa fa-trash-o'
            ]
        ];
    }



    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeButton($id,$status)
    {
        if($status==0)
        {
            return [
                '详情' => [
                    'auth' => 'goods/det',
                    'href' => "javascript:goodsDet('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
                '编辑' => [
                    'auth' => 'goods/edit',
                    'href' => "javascript:goodsEdit('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-pencil'
                ],
                '删除' => [
                    'auth' => 'goods/del',
                    'href' => "javascript:goodsDel('$id')",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ],
                '上架' => [
                    'auth' => 'goods/check',
                    'href' => "javascript:goodsCheck('$id')",
                    'btnStyle' => 'info',
                    'icon' => 'fa fa-check-square-o'
                ]
            ];
        }else{
            return [
                '详情' => [
                    'auth' => 'goods/edit',
                    'href' => "javascript:goodsDet('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
                '编辑' => [
                    'auth' => 'goods/det',
                    'href' => "javascript:goodsEdit('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-pencil'
                ],
                '删除' => [
                    'auth' => 'goods/del',
                    'href' => "javascript:goodsDel('$id')",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ],
                '下架' => [
                    'auth' => 'goods/check',
                    'href' => "javascript:goodsCheck('$id')",
                    'btnStyle' => 'info',
                    'icon' => 'fa fa-check-square-o'
                ]
            ];
        }

    }

}
