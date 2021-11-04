<?php
namespace backend\controllers;

use backend\models\Admins;
use backend\models\Agents;
use backend\models\Agentsgoodsprice;
use backend\models\Area;
use common\helpers\Tools;
use backend\models\Goods;
use backend\models\Dictionary;
use yii\db\Connection;
use yii\db\Query;
use yii\web\Response;

class AgentsController extends BaseController
{
    public function actionIndex()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];

            if (!empty($param['searchText'])) {
                $where = ['like', $param['searchType'], $param['searchText'],];
            } elseif($param['checkStatus']!="#") {
                $where = ['like', 'status', $param['checkStatus']];
            } else {
                $where = ['like', $param['searchType'], $param['searchText'],];
            }

            $selectResult = Agents::getAgentsByWhere($where, $offset, $limit);
                //地区3级
            foreach ($selectResult as $key => $value){
                if(!empty($value['region'])){
                    $area = Area::findOne($value['region']);
                    if(!empty($area)){
                        $areaParent = Area::findOne($area->Pid);
                        //$selectResult[$key]['region'] = $areaParent->Name . $area->Name;
                        if($areaParent->Pid!='0'){
                            $areaParentParent = Area::findOne($areaParent->Pid);
                            if(!empty($areaParentParent)){
                                $selectResult[$key]['region'] = $areaParentParent->Name . $areaParent->Name . $area->Name;
                            }
                        }else{
                            $selectResult[$key]['region'] = $areaParent->Name .$area->Name;
                        }
                    }
                } else {
                    $selectResult[$key]['region'] = "";
                }
            }
            $status = Agents::getStatus();
            // 拼装参数
            foreach($selectResult as $key => $vo){

                // 判断身份状态
                if($vo['status']){
                    $status = Agents::getAppStatus();
                    isset($status[$vo['status']]) && $selectResult[$key]['status'] = $status[isset($vo['Appstatus'])?$vo['Appstatus']:'0'];
                }else{
                    isset($status[$vo['status']]) && $selectResult[$key]['status'] = $status[$vo['status']];
                }



                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['id'],$vo['status']));

            }

            $return['total'] = Agents::getAgentNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }

        return $this->render('index');
    }

    // 添加代理商
    public function actionAdd()
    {
        $request = \Yii::$app->request;

        if($request->isPost){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $param = $request->post();
            //var_dump($param);
            $param['password'] = md5($param['password'] . \Yii::$app->params['salt']);
            $param['id'] = Tools::create_id();


            //添加到agents表后再添加到pay_admin表
            $agent = new Agents();
            $res = $agent->addAgents($param);
            if($res['code']==1 && $param['status'] == '1') {
                $admin = new Admins();
                $adminRes = $admin->addAgents($param);
                return $adminRes;
            } else {
                return $res;
            }

            //添加事务  不成功
//            $transaction = \Yii::$app->db->beginTransaction();
//            try{
//                $agent = new Agents();
//                $res = $agent->addAgents($param);
//                if($res['code']!= 1){
//                    $e = $res['msg'];
//                    throw new \Exception($e);//抛出异常
//                } else {
//                    $admin = new Admins();
//                    $adminRes = $admin->addAgents($param);
//                    if($adminRes['code'] != 1){
//                        $e = $adminRes['msg'];
//                        throw new \Exception($e);//抛出异常
//                    }
//                }
//                $transaction->commit();
//                return ['code' => 1, 'data' => '', 'msg' => '添加代理商成功'];
//            }catch (\Exception $e) {
//                $transaction->rollBack();
//                throw $e;
//                //return ['code' => -1, 'data' => '', 'msg' => '添加代理商失败'];
//            }

        }

        return $this->render('add');
    }

    // 编辑代理商
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


            $admin = new Agents();
            $res = $admin->editAgents($param);

            return $res;
        }

        $info = Agents::getAgentById($request->get('id'));

        //地区3级
        $area = Area::findOne($info['region']);
        $region =[];
        if(!empty($area)){
            $areaParent = Area::findOne($area->Pid);
            if($areaParent->Pid!='0'){
                $areaParentParent = Area::findOne($areaParent->Pid);
                if(!empty($areaParentParent)){
                    //$info['region'] = $areaParentParent->Name . $areaParent->Name . $area->Name;
                    $region['areaParentParentName'] = $areaParentParent->Name;
                    $region['areaParentName'] = $areaParent->Name;
                    $region['areaName'] =  $area->Name;
                }

            }else{
                //$info['region'] = $areaParent->Name .$area->Name;
                //$region['areaParentParentParentName'] = "";
                $region['areaParentParentName'] = $areaParent->Name;
                $region['areaParentName'] = $areaParent->Name;
                $region['areaName'] =  $area->Name;
            }
        }
        //状态
        $status = Agents::getStatus();
        $info['status'] =  $status[$info['status']];

        return $this->render('edit', [
            'info' => $info,
            'region' => $region
        ]);
    }

    //代理商信息详情
    public function actionDet()
    {
        $request = \Yii::$app->request;

        $info = Agents::getAgentById($request->get('id'));

        //地区3级
        $area = Area::findOne($info['region']);
        if(!empty($area)){
            $areaParent = Area::findOne($area->Pid);
            if($areaParent->Pid!='0'){
                $areaParentParent = Area::findOne($areaParent->Pid);
                if(!empty($areaParentParent)){
                    $info['region'] = $areaParentParent->Name . $areaParent->Name . $area->Name;
                }
            }else{
                $info['region'] = $areaParent->Name .$area->Name;
            }
        }

        //状态
        $status = Agents::getStatus();
        $info['status'] =  $status[$info['status']];
        //添加时间
        $info['addAt']=date("Y-m-d H:i:s",$info['addAt']);

        return $this->render('det', [
            'info' => $info
        ]);
    }

    // 删除单个代理商信息
    public function actionDel()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $agent = new Agents();
            $res = $agent->delAgent($id);

            //添加到agents表后再添加到pay_admin表
            if($res['code']==1) {
                $admin = new Admins();
                $adminRes = $admin->delAgent($id);
                return $adminRes;
            } else {
                return $res;
            }
        }
    }
    //批量删除
    public function actionDelSelected()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $ids = $request->get('ids');

            $agent = new Agents();
            $res = $agent->delAgentSelected($ids);

            return $res;
        }
    }

    // 审核
    public function actionCheck()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $res = Agents::checkAgent($id);
            return $res;

        }
    }
    
public function actionPrice(){
    $agentID = \Yii::$app->request->get('id');
    //获取所有商品ID
    $goods = Goods::find()->select(['id', 'title'])->all();
    if(\Yii::$app->request->isPost){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $post = \Yii::$app->request->post();
        $agentID = $post['id'];
        $ver = Agentsgoodsprice::getVer($agentID, $post['goodsID']);
        $price = new Agentsgoodsprice();
        $price->id = Tools::create_id();
        $price->agentID = $agentID;
        $price->goodsID = $post['goodsID'];
        $price->price = $post['price'];
        $price->ver = isset($ver)?$ver+1:1;
        $price->addAt = time();
        $price->updateAt = time();
        $price->addBy = $agentID;
        $price->addIP = Tools::getClientIp();
        $price->addAgent = Tools::browse_info();
        if(!$price->save()){
            $res = ['code' => -1, 'data' => '', 'msg' => '添加价格信息失败'];
            return $res;
        }else{
            $res = ['code' => 1, 'data' => '', 'msg' => '添加价格信息成功'];
            return $res;
        }
    }

    $price = (new Query())
        ->select(['goodsID'])
        ->from(Agentsgoodsprice::tableName())
//        ->join('LEFT JOIN', Goods::tableName(), 'goods.id = agentsgoodsprice.goodsID')
        ->where(['agentID' => $agentID])
        ->all();
    $info = '';
    if($price) {
        $prc = '';
        foreach ($price as $vo) {
            $prc[] = $vo['goodsID'];
        }

        $prc = array_unique($prc);
        $prc = array_values($prc);
        $i = 0;
        foreach ($prc as $vo) {
            $ver = Agentsgoodsprice::getVer($agentID, $vo);
            $res = (new Query())
                ->select(['Agentsgoodsprice.price', 'goods.title'])
                ->from(Agentsgoodsprice::tableName())
                ->join('LEFT JOIN', Goods::tableName(), 'goods.id = agentsgoodsprice.goodsID')
                ->where(['agentsgoodsprice.agentID' => $agentID, 'agentsgoodsprice.goodsID' => $vo, 'agentsgoodsprice.ver' => $ver])
                ->one();
            $info[$i]['price'] = $res['price'];
            $info[$i]['title'] = $res['title'];
            $i++;
        }
    }

    return $this->render('price', [
        'goods' => $goods,
        'id' => $agentID,
        'info' => $info
    ]);
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
                    'auth' => 'agents/det',
                    'href' => "javascript:agentsDet('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
                '编辑' => [
                    'auth' => 'agents/edit',
                    'href' => "javascript:agentsEdit('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-pencil'
                ],
                '价格设置' => [
                    'auth' => '55',
                    'href' => "javascript:agentsPrice('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-bitcoin'
                ],
                '删除' => [
                    'auth' => 'agents/del',
                    'href' => "javascript:agentsDel('$id')",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ],
                '审核' => [
                    'auth' => 'agents/check',
                    'href' => "javascript:agentsCheck('$id')",
                    'btnStyle' => 'info',
                    'icon' => 'fa fa-check-square-o'
                ]
            ];
        }else{
            return [
                '详情' => [
                    'auth' => 'agents/det',
                    'href' => "javascript:agentsDet('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
                '编辑' => [
                    'auth' => 'agents/edit',
                    'href' => "javascript:agentsEdit('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-pencil'
                ],
                '价格设置' => [
                    'auth' => 'agents/price',
                    'href' => "javascript:agentsPrice('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-bitcoin'
                ],
                '删除' => [
                    'auth' => 'agents/del',
                    'href' => "javascript:agentsDel('$id')",
                    'btnStyle' => 'danger',
                    'icon' => 'fa fa-trash-o'
                ]
        ];
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
}