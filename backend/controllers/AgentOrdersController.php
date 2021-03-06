<?php
namespace backend\controllers;

use backend\models\Agentaddr;
use backend\models\AgentOrders;
use backend\models\Agentordersdetail;
use backend\models\Agentordertrack;
use backend\models\Agents;
use backend\models\Area;
use backend\models\Goods;
use backend\models\Goodsattach;
use backend\models\Skubasic;
use backend\models\Skuingoods;
use common\helpers\Tools;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use backend\models\Users;
use backend\models\WxMessage;
use common\models\Logistics;
require_once __DIR__.'/../web/component/excel/PHPExcel/IOFactory.php';

class AgentOrdersController extends BaseController
{
    public function actionIndex()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];

            if (!empty($param['searchText']) && $param['checkStatus']!="#") {
//                $agents = Agents::find()->where(['like','contactPhone',$param['searchText']])->all();
//                $ids = ArrayHelper::getColumn($agents, 'id');
//                $where = ['and',['like', 'status', $param['checkStatus']],['in', 'agentID', $ids]];
                $addr = Agentaddr::find()->where(['like','phone',$param['searchText']])->all();
                $ids = ArrayHelper::getColumn($addr, 'agentID');
                $where = ['and',['like', 'status', $param['checkStatus']],['in', 'agentID', $ids]];
            } elseif($param['checkStatus']!="#" && empty($param['searchText'])) {
                $where = ['like', 'status', $param['checkStatus']];
            } elseif($param['checkStatus']=="#" && !empty($param['searchText'])) {
                //var_dump("1");
//                $agents = Agents::find()->where(['like','contactPhone',$param['searchText']])->all();
//                $ids = ArrayHelper::getColumn($agents, 'id');
//                $where = ['in', 'agentID', $ids];
                $addr = Agentaddr::find()->where(['like','phone',$param['searchText']])->all();
                $ids = ArrayHelper::getColumn($addr, 'agentID');
                $where = ['in', 'agentID', $ids];
            } else {
                $where = [];
            }

            $selectResult = AgentOrders::getAgentOrdersByWhere($where, $offset, $limit);

            $status = AgentOrders::getStatus();
            // ????????????
            foreach($selectResult as $key => $vo){

                // ??????
                isset($status[$vo['status']]) && $selectResult[$key]['status'] = $status[$vo['status']];
                //??????
                $selectResult[$key]['addAt']=date("Y-m-d H:i:s",$selectResult[$key]['addAt']);
                $selectResult[$key]['updateAt']=date("Y-m-d H:i:s",$selectResult[$key]['updateAt']);

                //??????
//                $addr = Agentaddr::find()->where(['id' =>$vo['agentAddrID']])->one();
//                $selectResult[$key]['phone'] = $addr['phone'];
//                $agent = Agents::findOne(['id' => $vo['agentID']]);
//                $selectResult[$key]['phone'] = $agent['contactPhone'];
                //$agentaddr = Agentaddr::findOne(['agentID' => $vo['agentID']]);
                $agentaddr = Agentaddr::findOne(['id' => $vo['agentAddrID']]);
                $selectResult[$key]['phone'] = $agentaddr['phone'];
                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['ID'],$vo['status']));

            }

            $return['total'] = AgentOrders::getAgentOrdersNum($where);  // ?????????
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }

        return $this->render('index');
    }

    //????????????
    public function actionDet()
    {
        $request = \Yii::$app->request;

        $info = AgentOrders::getOrdersById($request->get('id'));

        //??????
        $addr = Agentaddr::find()->where(['id' =>$info['agentAddrID']])->one();
        $info['agentAddr'] = '';
        if(!empty($addr)){
            $area = Area::find()->where(['Id' =>$addr['regionID']])->one();
            if(!empty($area)){
                $areaParent = Area::findOne($area->Pid);
                if($areaParent->Pid!='0'){
                    $areaParentParent = Area::findOne($areaParent->Pid);
                    if(!empty($areaParentParent)){
                        $info['agentAddr'] =$areaParentParent->Name . $areaParent->Name . $area->Name .$addr['addr'];
                    }
                }else{
                    $info['agentAddr'] =$areaParent->Name .$area->Name.$addr['addr'];
                }
            }
        }
        //???????????????
        $info['receiver'] = $addr['name'];
        $info['receiverPhone'] = $addr['phone'];


        //??????
        $status = AgentOrders::getStatus();
        $info['status'] =  $status[$info['status']];
        //??????
        $info['addAt'] = date("Y-m-d H:i:s",$info['addAt']);

        $agent=Agents::find()->where(['id' => $info['agentID']])->one();
        //$info['agentName']=Agents::findBySql('select contactName from Agents ')->where(['id' => $info['agentID']])->one();
        $info['agentName']=$agent['contactName'];
        $info['phone']=$agent['contactPhone'];

        $details = Agentordersdetail::find()->where(['orderID' => $request->get('id')])->asArray()->all();
        foreach ($details as $key => $value){
            $skuingood = Skuingoods::findOne($details[$key]['skuInGoodsID']);
            $sku = Skubasic::findOne($skuingood->skuID);
            $details[$key]['name'] = $sku->Name;
            $details[$key]['image'] = $skuingood['images'];
            //????????????
            $good = Goods::findOne(['id' => $skuingood['goodsID']]);
            $details[$key]['title'] = $good['title'];
        }
        return $this->render('det', [
            'info' => $info,
            'details' => $details,
        ]);
    }

    // ??????????????????
    public function actionDel()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $admin = new AgentOrders();
            $res = $admin->delAgentOrders($id);

            return $res;
        }
    }



    //????????????
    public function actionDelSelected()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $ids = $request->get('ids');

            $agent = new AgentOrders();
            $res = $agent->delOrdersSelected($ids);

            return $res;
        }
    }

    // ??????????????????
    public function actionComplete()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->get('id');

            $admin = new AgentOrders();
            $res = $admin->comAgentOrders($id);

            return $res;
        }
    }

    //????????????
    public function actionComSelected()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $ids = $request->get('ids');

            $agent = new AgentOrders();
            $res = $agent->comOrdersSelected($ids);

            return $res;
        }
    }

    //??????
    public function actionDeliv()
    {
        $request = \Yii::$app->request;

        if($request->isPost){
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $request->post('id');
            $trackID = $request->post('trackID');
            $order = new AgentOrders();
            $res = $order->Deliv($id,$trackID);

            if($res['code'] == 1){
                $aorder = AgentOrders::findOne($id);
                $user = Users::findOne($aorder['agentID']);
                //??????????????????
                $WxMessage = new WxMessage();
                $resMessage = $WxMessage->send_notice($user['openID'],$trackID,"????????????");
            }
            return $res;
        }


        $id = $request->get('id');
        return $this->render('deliv',[
            'id' => $id
        ]);
    }

    //????????????
    public function actionTrack()//??????????????????????????????,??????all?????????,???????????????????????????????????????,????????????????????????all???,???????????????
    {
        $request = \Yii::$app->request;
        //????????????????????????????????????
        $id = $request->get('id');
        $orderTrack = Agentordertrack::find()->where(['orderID' => $id])->orderBy(['addAt' => SORT_DESC])->all();
        $all = [];
        $i = 0;
        foreach ($orderTrack as $key => $value){
            $all[$key]['content'] = $value['content'];
            $all[$key]['time'] = $value['addAt'];
            $i++;
        }

        //???????????????????????????
        $trackID = $request->get('trackID');//????????????
        $Logistics = new Logistics();
        $res = $Logistics->express("kuayue",$trackID);//??????????????????
        $res = Tools::trimall($res);//?????????
        $res = json_decode($res,true);//??????
        if(!empty($res['showapi_res_body']['data'])){
            $express = $res['showapi_res_body']['data'];
            foreach ($express as $key => $value){
                $all[$i+$key]['time'] = (string)strtotime($value['time']);
                $all[$i+$key]['content'] = $value['context'];
                //$i++;
            }
        }

        //???????????????
        $datetime = array();

        foreach ($all as $value){
            $datetime[] = $value['time'];
        }
        array_multisort($datetime,SORT_DESC,$all);
        return $this->render('track',[
            'all' => $all
        ]);
    }

    /**
     * ?????????????????????
     */
    public function actionAgentExcel()
    {
        $request = \Yii::$app->request;
        $status = $request->get('status');
        \Yii::$app->response->format = Response::FORMAT_JSON;
        //$selectResult = AgentOrders::find()->orderBy(['addAt' => SORT_DESC])->asArray()->all();
        if($status == "") {
            $selectResult = AgentOrders::find()->orderBy(['addAt' => SORT_DESC])->asArray()->all();
        } else {
            $selectResult = AgentOrders::find()->where(['status' => $status])->orderBy(['addAt' => SORT_DESC])->asArray()->all();
        }


        // ????????????
        foreach($selectResult as $key => $vo){

            //??????
            $newAddr = Agentaddr::findOne($selectResult[$key]['agentAddrID']);
            //??????
            $selectResult[$key]['name'] = $newAddr['name'];
            //?????????
            $selectResult[$key]['phone'] = $newAddr['phone'];
            //$addr = Agentaddr::find()->where(['id' =>$info['agentAddrID']])->one();
            //????????????
            $selectResult[$key]['agentAddr'] = '';
            if(!empty($newAddr)){
                $area = Area::find()->where(['Id' =>$newAddr['regionID']])->one();
                if(!empty($area)){
                    $areaParent = Area::findOne($area->Pid);
                    if($areaParent->Pid!='0'){
                        $areaParentParent = Area::findOne($areaParent->Pid);
                        if(!empty($areaParentParent)){
                            $selectResult[$key]['agentAddr'] =$areaParentParent->Name . $areaParent->Name . $area->Name .$newAddr['addr'];
                        }
                    }else{
                        $selectResult[$key]['agentAddr'] =$areaParent->Name .$area->Name.$newAddr['addr'];
                    }
                }
            }
        }

        $PHPExcel = new \PHPExcel();
        $PHPExcel->getProperties()->setTitle('???????????????');
        $PHPExcel->setActiveSheetIndex(0);

        //????????????
        $PHPExcel->getActiveSheet()->setCellValue('A1',  '???????????????');
        //????????????
        $PHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //???????????????
        $PHPExcel->getActiveSheet()->mergeCells('A1:G1');
        $PHPExcel->getActiveSheet()->setCellValue("A2",'??????');
        $PHPExcel->getActiveSheet()->setCellValue("B2",'?????????');
        $PHPExcel->getActiveSheet()->setCellValue("C2",'??????');
        $PHPExcel->getActiveSheet()->setCellValue("D2",'??????');
        $PHPExcel->getActiveSheet()->setCellValue("E2",'????????????');
        $PHPExcel->getActiveSheet()->setCellValue("F2",'????????????');
        $PHPExcel->getActiveSheet()->setCellValue("G2",'????????????');
        $i = 3;

        foreach ($selectResult as $key => $value){
            $PHPExcel->getActiveSheet()->setCellValue("A".$i,$value['name']);
            $PHPExcel->getActiveSheet()->setCellValue("B".$i,$value['phone']);
            $PHPExcel->getActiveSheet()->setCellValue("C".$i,$selectResult[$key]['agentAddr']);
            $PHPExcel->getActiveSheet()->setCellValue("D".$i,$value['memo']);
            $PHPExcel->getActiveSheet()->setCellValue("E".$i,$value['totalNum']);
            $PHPExcel->getActiveSheet()->setCellValue("F".$i,"???????????????");
            $PHPExcel->getActiveSheet()->setCellValue("G".$i,$value['totalMoney']);
            $i++;
        }
        $Statu = AgentOrders::getStatus();
        header('Content-Type : application/vnd.ms-excel;charset=utf-8');
        if($status != ''){
            header('Content-Disposition:attachment;filename="???????????????'.date("Y-m-d H:i:s").' '.$Statu[$status].' .xls"');
        } else {
            header('Content-Disposition:attachment;filename="???????????????'.date("Y-m-d H:i:s").'??????.xls"');
        }

        $objWriter= \PHPExcel_IOFactory::createWriter($PHPExcel,'Excel5');

        $objWriter->save('php://output');
        exit();
    }

    /**
     * ??????????????????
     * @param $id
     * @return array
     */
    private function makeButton($id,$status)
    {
        if($status != 1)//?????????????????????
        {
            if($status != 3)
            {
                return [
                    '??????' => [
                        'auth' => 'agent-orders/det',
                        'href' => "javascript:ordersDet('$id')",
                        'btnStyle' => 'primary',
                        'icon' => 'fa fa-paste'
                    ],
//                '??????' => [
//                    'auth' => 'agent-orders/del',
//                    'href' => "javascript:ordersDel('$id')",
//                    'btnStyle' => 'danger',
//                    'icon' => 'fa fa-trash-o'
//                ],
                    '??????' => [
                        'auth' => 'agent-orders/complete',
                        'href' => "javascript:ordersComplete('$id')",
                        'btnStyle' => 'warning',
                        'icon' => 'fa fa-check-o'
                    ]
                ];
            }
            return [
                '??????' => [
                    'auth' => 'agent-orders/det',
                    'href' => "javascript:ordersDet('$id')",
                    'btnStyle' => 'primary',
                    'icon' => 'fa fa-paste'
                ],
            ];

        }
        return [
            '??????' => [
                'auth' => 'agent-orders/deliv',
                'href' => "javascript:ordersDeliv('$id')",
                'btnStyle' => 'primary',
                'icon' => 'fa fa-ambulance'
            ],
            '??????' => [
                'auth' => 'agent-orders/det',
                'href' => "javascript:ordersDet('$id')",
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],
        ];
    }
}