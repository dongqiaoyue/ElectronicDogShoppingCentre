<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2019/7/18
 * Time: 17:23
 */
namespace backend\controllers;

use backend\models\Useraddr;
use backend\models\Userordersdetail;
use backend\models\Userordertrack;
use backend\models\Users;
use backend\models\Goods;
use backend\models\Skuingoods;
use backend\models\Skubasic;
use backend\models\Area;
use yii\db\Query;
use common\helpers\Tools;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use backend\models\Userorders;
use backend\models\WxMessage;
use common\models\Logistics;
require_once __DIR__.'/../web/component/excel/PHPExcel/IOFactory.php';

class UordersController extends BaseController
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
//                $users = Users::find()->where(['like','phone',$param['searchText']])->all();
//                $ids = ArrayHelper::getColumn($users, 'id');
//                $where = ['and',['like', 'status', $param['checkStatus']],['in', 'userID', $ids]];
                $addr = Useraddr::find()->where(['like','phone',$param['searchText']])->all();
                $ids = ArrayHelper::getColumn($addr, 'userID');
                $where = ['and',['like', 'status', $param['checkStatus']],['in', 'userID', $ids]];
            } elseif($param['checkStatus']!="#" && empty($param['searchText'])) {
                $where = ['like', 'status', $param['checkStatus']];
            } elseif($param['checkStatus']=="#" && !empty($param['searchText'])) {
                //var_dump("1");
//                $users = Useraddr::find()->where(['like','phone',$param['searchText']])->all();
//                $ids = ArrayHelper::getColumn($users, 'id');
//                $where = ['in', 'userID', $ids];
                $addr = Useraddr::find()->where(['like','phone',$param['searchText']])->all();
                $ids = ArrayHelper::getColumn($addr, 'userID');
                $where = ['in', 'userID', $ids];
            } else {
                $where = [];
            }

            $selectResult = Userorders::getUserOrdersByWhere($where, $offset, $limit);

            $status = Userorders::getStatus();

            // ????????????
            foreach($selectResult as $key => $vo){
                $phone = Users::find()->select('phone')->where(['id' => $selectResult[$key]['userID']])->one();
                if(!empty($phone)){
                    $selectResult[$key]['phone'] = $phone->phone;
                } else {
                    $selectResult[$key]['phone'] = "";
                }

                //??????????????????
                $add_time = $selectResult[$key]['addAt'];
                $selectResult[$key]['addAt'] = date("Y-m-d H:i:s", $add_time);
                $selectResult[$key]['updateAt'] = date("Y-m-d H:i:s", $selectResult[$key]['updateAt']);

                // ??????
                isset($status[$vo['status']]) && $selectResult[$key]['status'] = $status[$vo['status']];

                //?????????
                //??????
//                $addr = Useraddr::find()->where(['id' =>$vo['userAddrID']])->one();
//                $selectResult[$key]['phone'] = $addr['phone'];
//                $user = Users::findOne(['id' => $vo['userID']]);
//                $selectResult[$key]['phone'] = $user['phone'];
                //$useraddr = Useraddr::findOne(['userID' => $vo['userID']]);
                $useraddr = Useraddr::findOne(['id' => $vo['userAddrID']]);
                $selectResult[$key]['phone'] = $useraddr['phone'];
                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['ID'],$vo['status']));
            }

            $return['total'] = Userorders::getUordersNum($where);  // ?????????
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }

        return $this->render('index');
    }

    public function actionDet()
    {
        $request = \Yii::$app->request;

        //????????????????????????????????????????????????
        $info = (new Query())
            ->select(['users.id', 'users.phone', 'userorders.ID', 'userorders.userAddrID', 'userorders.totalMoney', 'userorders.trackID', 'userorders.postName', 'userorders.memo', 'userorders.addAt', 'userorders.updateAt', 'userorders.status', 'useraddr.addr', 'useraddr.name'])
            ->from(Users::tableName())
            ->join('RIGHT JOIN', Userorders::tableName(), 'users.id = userorders.userID')
            ->join('LEFT JOIN', Useraddr::tableName(), 'userorders.userAddrID = useraddr.id')
            ->where(['userorders.ID' => $request->get('id')])
            ->one();
        //??????
        $info['addAt'] = date("Y-m-d H:i:s", $info['addAt']);
        $info['updateAt'] = date("Y-m-d H:i:s", $info['updateAt']);
        $status = Userorders::getStatus();
        $info['status'] = $status[$info['status']];

        //??????
        $addr = Useraddr::find()->where(['id' =>$info['userAddrID']])->one();
        $info['userAddr'] = '';
        if(!empty($addr)){
            $area = Area::find()->where(['Id' =>$addr['regionID']])->one();
            if(!empty($area)){
                $areaParent = Area::findOne($area->Pid);
                if($areaParent->Pid!='0'){
                    $areaParentParent = Area::findOne($areaParent->Pid);
                    if(!empty($areaParentParent)){
                        $info['userAddr'] =$areaParentParent->Name . $areaParent->Name . $area->Name .$addr['addr'];
                    }
                }else{
                    $info['userAddr'] =$areaParent->Name .$area->Name.$addr['addr'];
                }
            }
        }
        //???????????????
        $info['receiver'] = $addr['name'];
        $info['receiverPhone'] = $addr['phone'];

        $details = Userordersdetail::find()->where(['orderID' => $request->get('id')])->asArray()->all();
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

    //????????????
    public function actionDelSelected()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $ids = $request->get('ids');

            $order = new Userorders();
            $res = $order->delOrdersSelected($ids);

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

            $admin = new Userorders();
            $res = $admin->comUserOrders($id);

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

            $user = new Userorders();
            $res = $user->comOrdersSelected($ids);

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
            $order = new Userorders();
            $res = $order->Deliv($id,$trackID);

            if($res['code'] == 1){
                $uorder = Userorders::findOne($id);
                $user = Users::findOne($uorder['userID']);
                //??????????????????
                $WxMessage = new WxMessage();
                $resMessage = $WxMessage->send_notice($user['openID'],$trackID,"????????????");
            }
            return $res;
            //return [$res,$resMessage];
        }

        $id = $request->get('id');
        return $this->render('deliv',[
            'id' => $id
        ]);
    }
    
    //????????????
    public function actionTrack()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id');
        $orderTrack = Userordertrack::find()->where(['orderID' => $id])->orderBy(['addAt' => SORT_DESC])->all();

        $all = [];//???????????????????????????
        $i = 0;
        foreach ($orderTrack as $key => $value){
            $all[$key]['content'] = $value['content'];
            $all[$key]['time'] = $value['addAt'];
            $i++;
        }

        //???????????????????????????
        $trackID = $request->get('trackID');//????????????
        $Logistics = new Logistics();
        $res = $Logistics->express("shunfeng",$trackID);//???????????????a
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
     * ??????????????????
     */
    public function actionUserExcel()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $request = \Yii::$app->request;
        $status = $request->get('status');
        //return $status;
        if($status == ""){
            $selectResult = UserOrders::find()->orderBy(['addAt' => SORT_DESC])->asArray()->all();
        } else{
            $selectResult = UserOrders::find()->where(['status' => $status])->orderBy(['addAt' => SORT_DESC])->asArray()->all();
        }

        //$selectResult = AgentOrders::find()->orderBy(['addAt' => SORT_DESC])->asArray()->all();


        // ????????????
        foreach($selectResult as $key => $vo){

            //??????
            $newAddr = Useraddr::findOne($selectResult[$key]['userAddrID']);
            //??????
            $selectResult[$key]['name'] = $newAddr['name'];
            //?????????
            $selectResult[$key]['phone'] = $newAddr['phone'];
            //$addr = Agentaddr::find()->where(['id' =>$info['agentAddrID']])->one();
            //????????????
            $selectResult[$key]['addr'] = '';
            if(!empty($newAddr)){
                $area = Area::find()->where(['Id' =>$newAddr['regionID']])->one();
                if(!empty($area)){
                    $areaParent = Area::findOne($area->Pid);
                    if($areaParent->Pid!='0'){
                        $areaParentParent = Area::findOne($areaParent->Pid);
                        if(!empty($areaParentParent)){
                            $selectResult[$key]['addr'] =$areaParentParent->Name . $areaParent->Name . $area->Name .$newAddr['addr'];
                        }
                    }else{
                        $selectResult[$key]['addr'] =$areaParent->Name .$area->Name.$newAddr['addr'];
                    }
                }
            }
            //????????????
            $selectResult[$key]['totalNum'] ='';
            $orderDetails = (new Query())
                ->select('numbers')
                ->from('userordersdetail')
                ->where(['orderID' => $vo['ID']])
                //->andWhere((['in','skuInGoodsID',$skuingoodIDs]))
                ->all();
            $count = 0;
            foreach ($orderDetails as $value){
                $count += $value['numbers'];
            }
            $selectResult[$key]['totalNum'] = $count;
        }
        //return $selectResult;
        $PHPExcel = new \PHPExcel();
        $PHPExcel->getProperties()->setTitle('????????????');
        $PHPExcel->setActiveSheetIndex(0);

        //????????????
        $PHPExcel->getActiveSheet()->setCellValue('A1',  '????????????');
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
            $PHPExcel->getActiveSheet()->setCellValue("C".$i,$value['addr']);
            $PHPExcel->getActiveSheet()->setCellValue("D".$i,$value['memo']);
            $PHPExcel->getActiveSheet()->setCellValue("E".$i,$value['totalNum']);
            $PHPExcel->getActiveSheet()->setCellValue("F".$i,"????????????");
            $PHPExcel->getActiveSheet()->setCellValue("G".$i,$value['totalMoney']);
            $i++;
        }
        $Statu = UserOrders::getStatus();
        header('Content-Type : application/vnd.ms-excel;charset=utf-8');
        if($status != ''){
            header('Content-Disposition:attachment;filename="????????????'.date("Y-m-d H:i:s").''.$Statu[$status].' .xls"');
        } else {
            header('Content-Disposition:attachment;filename="????????????'.date("Y-m-d H:i:s").'??????.xls"');
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
                        'auth' => 'uorders/det',
                        'href' => "javascript:uordersDet('$id')",
                        'btnStyle' => 'info',
                        'icon' => 'fa fa-paste'
                    ],
                    '??????' => [
                        'auth' => 'uorders/complete',
                        'href' => "javascript:uordersComplete('$id')",
                        'btnStyle' => 'warning',
                        'icon' => 'fa fa-check-o'
                    ]
                ];
            }
            return [
                '??????' => [
                    'auth' => 'uorders/det',
                    'href' => "javascript:uordersDet('$id')",
                    'btnStyle' => 'info',
                    'icon' => 'fa fa-paste'
                ],
            ];
        }
        return [
            '??????' => [
                'auth' => 'uorders/deliv',
                'href' => "javascript:uordersDeliv('$id')",
                'btnStyle' => 'primary',
                'icon' => 'fa fa-ambulance'
            ],
            '??????' => [
                'auth' => 'uorders/det',
                'href' => "javascript:uordersDet('$id')",
                'btnStyle' => 'info',
                'icon' => 'fa fa-paste'
            ],
        ];
    }
}