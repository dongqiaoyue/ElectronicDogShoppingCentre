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

            // 拼装参数
            foreach($selectResult as $key => $vo){
                $phone = Users::find()->select('phone')->where(['id' => $selectResult[$key]['userID']])->one();
                if(!empty($phone)){
                    $selectResult[$key]['phone'] = $phone->phone;
                } else {
                    $selectResult[$key]['phone'] = "";
                }

                //还原时间格式
                $add_time = $selectResult[$key]['addAt'];
                $selectResult[$key]['addAt'] = date("Y-m-d H:i:s", $add_time);
                $selectResult[$key]['updateAt'] = date("Y-m-d H:i:s", $selectResult[$key]['updateAt']);

                // 状态
                isset($status[$vo['status']]) && $selectResult[$key]['status'] = $status[$vo['status']];

                //手机号
                //地址
//                $addr = Useraddr::find()->where(['id' =>$vo['userAddrID']])->one();
//                $selectResult[$key]['phone'] = $addr['phone'];
//                $user = Users::findOne(['id' => $vo['userID']]);
//                $selectResult[$key]['phone'] = $user['phone'];
                //$useraddr = Useraddr::findOne(['userID' => $vo['userID']]);
                $useraddr = Useraddr::findOne(['id' => $vo['userAddrID']]);
                $selectResult[$key]['phone'] = $useraddr['phone'];
                $selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['ID'],$vo['status']));
            }

            $return['total'] = Userorders::getUordersNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }

        return $this->render('index');
    }

    public function actionDet()
    {
        $request = \Yii::$app->request;

        //用户表关联用户订单表，用户地址表
        $info = (new Query())
            ->select(['users.id', 'users.phone', 'userorders.ID', 'userorders.userAddrID', 'userorders.totalMoney', 'userorders.trackID', 'userorders.postName', 'userorders.memo', 'userorders.addAt', 'userorders.updateAt', 'userorders.status', 'useraddr.addr', 'useraddr.name'])
            ->from(Users::tableName())
            ->join('RIGHT JOIN', Userorders::tableName(), 'users.id = userorders.userID')
            ->join('LEFT JOIN', Useraddr::tableName(), 'userorders.userAddrID = useraddr.id')
            ->where(['userorders.ID' => $request->get('id')])
            ->one();
        //时间
        $info['addAt'] = date("Y-m-d H:i:s", $info['addAt']);
        $info['updateAt'] = date("Y-m-d H:i:s", $info['updateAt']);
        $status = Userorders::getStatus();
        $info['status'] = $status[$info['status']];

        //地址
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
        //收货人信息
        $info['receiver'] = $addr['name'];
        $info['receiverPhone'] = $addr['phone'];

        $details = Userordersdetail::find()->where(['orderID' => $request->get('id')])->asArray()->all();
        foreach ($details as $key => $value){
            $skuingood = Skuingoods::findOne($details[$key]['skuInGoodsID']);
            $sku = Skubasic::findOne($skuingood->skuID);
            $details[$key]['name'] = $sku->Name;
            $details[$key]['image'] = $skuingood['images'];
            //商品名称
            $good = Goods::findOne(['id' => $skuingood['goodsID']]);
            $details[$key]['title'] = $good['title'];
        }

        return $this->render('det', [
            'info' => $info,
            'details' => $details,
        ]);
    }

    //批量删除
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

    // 完成订单信息
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

    //批量完成
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

    //发货
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
                //发送模板消息
                $WxMessage = new WxMessage();
                $resMessage = $WxMessage->send_notice($user['openID'],$trackID,"顺丰快递");
            }
            return $res;
            //return [$res,$resMessage];
        }

        $id = $request->get('id');
        return $this->render('deliv',[
            'id' => $id
        ]);
    }
    
    //订单跟踪
    public function actionTrack()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id');
        $orderTrack = Userordertrack::find()->where(['orderID' => $id])->orderBy(['addAt' => SORT_DESC])->all();

        $all = [];//存所有订单跟踪信息
        $i = 0;
        foreach ($orderTrack as $key => $value){
            $all[$key]['content'] = $value['content'];
            $all[$key]['time'] = $value['addAt'];
            $i++;
        }

        //调快递接口查询物流
        $trackID = $request->get('trackID');//快递单号
        $Logistics = new Logistics();
        $res = $Logistics->express("shunfeng",$trackID);//客户为顺丰a
        $res = Tools::trimall($res);//去空格
        $res = json_decode($res,true);//解码
        if(!empty($res['showapi_res_body']['data'])){
            $express = $res['showapi_res_body']['data'];
            foreach ($express as $key => $value){
                $all[$i+$key]['time'] = (string)strtotime($value['time']);
                $all[$i+$key]['content'] = $value['context'];
                //$i++;
            }
        }



        //按时间排序
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
     * 导出用户订单
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


        // 拼装参数
        foreach($selectResult as $key => $vo){

            //地址
            $newAddr = Useraddr::findOne($selectResult[$key]['userAddrID']);
            //姓名
            $selectResult[$key]['name'] = $newAddr['name'];
            //手机号
            $selectResult[$key]['phone'] = $newAddr['phone'];
            //$addr = Agentaddr::find()->where(['id' =>$info['agentAddrID']])->one();
            //详细地址
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
            //订单数量
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
        $PHPExcel->getProperties()->setTitle('用户订单');
        $PHPExcel->setActiveSheetIndex(0);

        //设置标题
        $PHPExcel->getActiveSheet()->setCellValue('A1',  '用户订单');
        //设置居中
        $PHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //合并单元格
        $PHPExcel->getActiveSheet()->mergeCells('A1:G1');

        $PHPExcel->getActiveSheet()->setCellValue("A2",'姓名');
        $PHPExcel->getActiveSheet()->setCellValue("B2",'手机号');
        $PHPExcel->getActiveSheet()->setCellValue("C2",'地址');
        $PHPExcel->getActiveSheet()->setCellValue("D2",'备注');
        $PHPExcel->getActiveSheet()->setCellValue("E2",'订单数量');
        $PHPExcel->getActiveSheet()->setCellValue("F2",'订单种类');
        $PHPExcel->getActiveSheet()->setCellValue("G2",'订单总价');
        $i = 3;

        foreach ($selectResult as $key => $value){
            $PHPExcel->getActiveSheet()->setCellValue("A".$i,$value['name']);
            $PHPExcel->getActiveSheet()->setCellValue("B".$i,$value['phone']);
            $PHPExcel->getActiveSheet()->setCellValue("C".$i,$value['addr']);
            $PHPExcel->getActiveSheet()->setCellValue("D".$i,$value['memo']);
            $PHPExcel->getActiveSheet()->setCellValue("E".$i,$value['totalNum']);
            $PHPExcel->getActiveSheet()->setCellValue("F".$i,"用户订单");
            $PHPExcel->getActiveSheet()->setCellValue("G".$i,$value['totalMoney']);
            $i++;
        }
        $Statu = UserOrders::getStatus();
        header('Content-Type : application/vnd.ms-excel;charset=utf-8');
        if($status != ''){
            header('Content-Disposition:attachment;filename="用户订单'.date("Y-m-d H:i:s").''.$Statu[$status].' .xls"');
        } else {
            header('Content-Disposition:attachment;filename="用户订单'.date("Y-m-d H:i:s").'全部.xls"');
        }
        $objWriter= \PHPExcel_IOFactory::createWriter($PHPExcel,'Excel5');

        $objWriter->save('php://output');
        exit();
    }

    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeButton($id,$status)
    {
        if($status != 1)//已付款才能发货
        {
            if($status != 3)
            {
                return [
                    '详情' => [
                        'auth' => 'uorders/det',
                        'href' => "javascript:uordersDet('$id')",
                        'btnStyle' => 'info',
                        'icon' => 'fa fa-paste'
                    ],
                    '完成' => [
                        'auth' => 'uorders/complete',
                        'href' => "javascript:uordersComplete('$id')",
                        'btnStyle' => 'warning',
                        'icon' => 'fa fa-check-o'
                    ]
                ];
            }
            return [
                '详情' => [
                    'auth' => 'uorders/det',
                    'href' => "javascript:uordersDet('$id')",
                    'btnStyle' => 'info',
                    'icon' => 'fa fa-paste'
                ],
            ];
        }
        return [
            '发货' => [
                'auth' => 'uorders/deliv',
                'href' => "javascript:uordersDeliv('$id')",
                'btnStyle' => 'primary',
                'icon' => 'fa fa-ambulance'
            ],
            '详情' => [
                'auth' => 'uorders/det',
                'href' => "javascript:uordersDet('$id')",
                'btnStyle' => 'info',
                'icon' => 'fa fa-paste'
            ],
        ];
    }
}