<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2019/7/17
 * Time: 15:14
 */
namespace backend\controllers;

use backend\models\Agentaddr;
use backend\models\AgentOrders;
use backend\models\Agentordersdetail;
use backend\models\Agents;
use backend\models\Area;
use backend\models\Skubasic;
use backend\models\Skuingoods;
use backend\models\Useraddr;
use backend\models\Userorders;
use common\helpers\Tools;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Response;
require_once __DIR__.'/../web/component/excel/PHPExcel/IOFactory.php';

class DataController extends BaseController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    //用户
    public function actionUserOrders()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];

            //地区
            $region = $param['region'];

            if(!empty($param['date']) && $region != '#'){
                //时间范围
                $dateArray = explode(" ",$param['date']);
                //时间
                $dateStart = strtotime(date($dateArray[0]));
                $dateEnd = strtotime(date($dateArray[2]));
                //地区
                $addr = (new Query())
                    ->select('id')
                    ->from('useraddr')
                    ->where(['like','regionID',substr($param['region'],0,2)])//根据省份前两位确定
                    ->all();
                //var_dump($addrID);
                //转化为数组
                foreach ($addr as $key => $value){
                    $addrID['id'][$key] = $value['id'];
                }
                $where = ['and',['status' => 3],['and',['between','addAt',$dateStart,$dateEnd],['in','userAddrID',$addrID['id']]]];
                //var_dump($where);
                //var_dump('1');
            } elseif(!empty($param['date']) && $region == '#') {
                //时间不为空,地区为空
                //时间范围
                $dateArray = explode(" ",$param['date']);
                //时间
                $dateStart = strtotime(date($dateArray[0]));
                $dateEnd = strtotime(date($dateArray[2]));
                $where = ['and',['between','addAt',$dateStart,$dateEnd],['status' => '3']];
                //var_dump('2');
            } elseif($region != '#') {
                //时间为空,地区不为空
                //地区
                $addr = (new Query())
                    ->select('id')
                    ->from('useraddr')
                    ->where(['like','regionID',substr($param['region'],0,2)])//根据省份前两位确定
                    ->all();

                foreach ($addr as $key => $value){
                    $addrID['id'][$key] = $value['id'];
                }

                $where = ['and',['in','userAddrID',$addrID['id']],['status' => '3']];
            } else {
                $where = ['status' => 3];
            }


            $selectResult = Userorders::getUserOrdersByWhere($where, $offset, $limit);

            $status = Userorders::getStatus();
            // 拼装参数
            foreach($selectResult as $key => $vo){

                // 状态
                isset($status[$vo['status']]) && $selectResult[$key]['status'] = $status[$vo['status']];
                //时间
                $selectResult[$key]['addAt']=date("Y-m-d H:i:s",$selectResult[$key]['addAt']);
                $selectResult[$key]['updateAt']=date("Y-m-d H:i:s",$selectResult[$key]['updateAt']);

                $selectResult[$key]['operate'] = Tools::showOperate($this->makeUserButton($vo['ID']));
                //地址
                $newAddr = Useraddr::findOne($selectResult[$key]['userAddrID']);

                //$addr = Agentaddr::find()->where(['id' =>$info['agentAddrID']])->one();
                if(!empty($newAddr)){
                    $area = Area::find()->where(['Id' =>$newAddr['regionID']])->one();
                    if(!empty($area)){
                        $areaParent = Area::findOne($area->Pid);
                        if($areaParent->Pid!='0'){
                            $areaParentParent = Area::findOne($areaParent->Pid);
                            if(!empty($areaParentParent)){
                                $selectResult[$key]['userAddr'] =$areaParentParent->Name . $areaParent->Name . $area->Name .$newAddr['addr'];
                            }
                        }else{
                            $selectResult[$key]['userAddr'] =$areaParent->Name .$area->Name.$newAddr['addr'];
                        }
                    }
                }

                //$selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['ID']));

            }

            $return['total'] = Userorders::getUOrdersNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }


        $province = (new Query())
            ->select(['Id','Name'])
            ->from('area')
            ->where(['Pid' => 0])
            ->andWhere(['not in','Id','0'])
            ->all();
        return $this->render('user-orders',[
            'province' => $province
        ]);
    }
    /**
     * 导出用户订单
     */
    public function actionUserExcel()
    {
        $param = \Yii::$app->request->get();
        \Yii::$app->response->format = Response::FORMAT_JSON;

        //地区
        $region = $param['region'];
        $regionName = Area::findOne($region);

        if(!empty($param['date']) && $region != '#'){
            //时间范围
            $dateArray = explode(" ",$param['date']);
            //时间
            $dateStart = strtotime(date($dateArray[0]));
            $dateEnd = strtotime(date($dateArray[2]));
            //地区
            $addr = (new Query())
                ->select('id')
                ->from('useraddr')
                ->where(['like','regionID',substr($param['region'],0,2)])//根据省份前两位确定
                ->all();
            //var_dump($addrID);
            //转化为数组
            foreach ($addr as $key => $value){
                $addrID['id'][$key] = $value['id'];
            }
            $where = ['and',['status' => 3],['and',['between','updateAt',$dateStart,$dateEnd],['in','userAddrID',$addrID['id']]]];

        } elseif(!empty($param['date']) && $region == '#') {
            //时间不为空,地区为空
            //时间范围
            $dateArray = explode(" ",$param['date']);
            //时间
            $dateStart = strtotime(date($dateArray[0]));
            $dateEnd = strtotime(date($dateArray[2]));
            $where = ['and',['between','updateAt',$dateStart,$dateEnd],['status' => '3']];
            //var_dump('2');
        } elseif($region != '#') {
            //时间为空,地区不为空
            //地区
            $addr = (new Query())
                ->select('id')
                ->from('useraddr')
                ->where(['like','regionID',substr($param['region'],0,2)])//根据省份前两位确定
                ->all();

            foreach ($addr as $key => $value){
                $addrID['id'][$key] = $value['id'];
            }

            $where = ['and',['in','userAddrID',$addrID['id']],['status' => '3']];
        } else {
            $where = ['status' => 3];
        }
        $selectResult = Userorders::find()->where($where)->orderBy(['addAt' => SORT_DESC])->asArray()->all();

        $status = Userorders::getStatus();
        // 拼装参数
        foreach($selectResult as $key => $vo){

            // 状态
            isset($status[$vo['status']]) && $selectResult[$key]['status'] = $status[$vo['status']];
            //时间
            $selectResult[$key]['addAt']=date("Y-m-d H:i:s",$selectResult[$key]['addAt']);
            $selectResult[$key]['updateAt']=date("Y-m-d H:i:s",$selectResult[$key]['updateAt']);

            //地址
            $newAddr = Useraddr::findOne($selectResult[$key]['userAddrID']);

            //$addr = Agentaddr::find()->where(['id' =>$info['agentAddrID']])->one();
            if(!empty($newAddr)){
                $area = Area::find()->where(['Id' =>$newAddr['regionID']])->one();
                if(!empty($area)){
                    $areaParent = Area::findOne($area->Pid);
                    if($areaParent->Pid!='0'){
                        $areaParentParent = Area::findOne($areaParent->Pid);
                        if(!empty($areaParentParent)){
                            $selectResult[$key]['userAddr'] =$areaParentParent->Name . $areaParent->Name . $area->Name .$newAddr['addr'];
                        }
                    }else{
                        $selectResult[$key]['userAddr'] =$areaParent->Name .$area->Name.$newAddr['addr'];
                    }
                }
            }
        }

        $PHPExcel = new \PHPExcel();
        $PHPExcel->getProperties()->setTitle('用户订单');
        $PHPExcel->setActiveSheetIndex(0);

        //设置标题
        $PHPExcel->getActiveSheet()->setCellValue('A1',  '用户订单'.$param['date'] .''.$regionName['Name'] .'');
        //设置居中
        $PHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //合并单元格
        $PHPExcel->getActiveSheet()->mergeCells('A1:G1');

        $PHPExcel->getActiveSheet()->setCellValue("A2",'订单号');
        $PHPExcel->getActiveSheet()->setCellValue("B2",'收货地址');
        $PHPExcel->getActiveSheet()->setCellValue("C2",'总价');
        $PHPExcel->getActiveSheet()->setCellValue("D2",'创建时间');
        $PHPExcel->getActiveSheet()->setCellValue("E2",'更新时间');
        $PHPExcel->getActiveSheet()->setCellValue("F2",'状态');
        $i = 3;

        foreach ($selectResult as $key => $value){
            $PHPExcel->getActiveSheet()->setCellValue("A".$i,$value['ID']);
            $PHPExcel->getActiveSheet()->setCellValue("B".$i,$value['userAddr']);
            $PHPExcel->getActiveSheet()->setCellValue("C".$i,$value['totalMoney']);
            $PHPExcel->getActiveSheet()->setCellValue("D".$i,$value['addAt']);
            $PHPExcel->getActiveSheet()->setCellValue("E".$i,$value['updateAt']);
            $PHPExcel->getActiveSheet()->setCellValue("F".$i,$value['status']);
            $i++;
        }

        header('Content-Type : application/vnd.ms-excel;charset=utf-8');
        header('Content-Disposition:attachment;filename="用户订单'.$param['date'].''.$regionName['Name'] .' .xls"');
        $objWriter= \PHPExcel_IOFactory::createWriter($PHPExcel,'Excel5');

        $objWriter->save('php://output');
        exit();
    }

    //代理商订单
    public function actionAgentOrders()
    {
        $request = \Yii::$app->request;

        if($request->isAjax){

            $param = $request->get();

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];

            //地区
            $region = $param['region'];

            if(!empty($param['date']) && $region != '#'){
                //时间范围
                $dateArray = explode(" ",$param['date']);
                //时间
                $dateStart = strtotime(date($dateArray[0]));
                $dateEnd = strtotime(date($dateArray[2]));
                //地区
                $addr = (new Query())
                    ->select('id')
                    ->from('agentaddr')
                    ->where(['like','regionID',substr($param['region'],0,2)])//根据省份前两位确定
                    ->all();
                //var_dump($addrID);
                //转化为数组
                foreach ($addr as $key => $value){
                    $addrID['id'][$key] = $value['id'];
                }
                $where = ['and',['status' => 3],['and',['between','addAt',$dateStart,$dateEnd],['in','agentAddrID',$addrID['id']]]];
                //var_dump($where);
                //var_dump('1');
            } elseif(!empty($param['date']) && $region == '#') {
                //时间不为空,地区为空
                //时间范围
                $dateArray = explode(" ",$param['date']);
                //时间
                $dateStart = strtotime(date($dateArray[0]));
                $dateEnd = strtotime(date($dateArray[2]));
                $where = ['and',['between','addAt',$dateStart,$dateEnd],['status' => '3']];
                //var_dump('2');
            } elseif($region != '#') {
                //时间为空,地区不为空
                //地区
                $addr = (new Query())
                    ->select('id')
                    ->from('agentaddr')
                    ->where(['like','regionID',substr($param['region'],0,2)])//根据省份前两位确定
                    ->all();

                foreach ($addr as $key => $value){
                    $addrID['id'][$key] = $value['id'];
                }

                $where = ['and',['in','agentAddrID',$addrID['id']],['status' => '3']];
            } else {
                $where = ['status' => 3];
            }


            $selectResult = AgentOrders::getAgentOrdersByWhere($where, $offset, $limit);

            $status = AgentOrders::getStatus();
            // 拼装参数
            foreach($selectResult as $key => $vo){

                // 状态
                isset($status[$vo['status']]) && $selectResult[$key]['status'] = $status[$vo['status']];
                //时间
                $selectResult[$key]['addAt']=date("Y-m-d H:i:s",$selectResult[$key]['addAt']);
                $selectResult[$key]['updateAt']=date("Y-m-d H:i:s",$selectResult[$key]['updateAt']);

                $selectResult[$key]['operate'] = Tools::showOperate($this->makeAgentButton($vo['ID']));

                //地址
                $newAddr = Agentaddr::findOne($selectResult[$key]['agentAddrID']);

                //$addr = Agentaddr::find()->where(['id' =>$info['agentAddrID']])->one();
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

                //$selectResult[$key]['operate'] = Tools::showOperate($this->makeButton($vo['ID']));

            }

            $return['total'] = AgentOrders::getAgentOrdersNum($where);  // 总数据
            $return['rows'] = $selectResult;

            return Tools::json($return);
        }


        $province = (new Query())
            ->select(['Id','Name'])
            ->from('area')
            ->where(['Pid' => 0])
            ->andWhere(['not in','Id','0'])
            ->all();
        return $this->render('agent-orders',[
            'province' => $province
        ]);
    }
    /**
     * 导出代理商订单
     */
    public function actionAgentExcel()
    {
        $param = \Yii::$app->request->get();
        \Yii::$app->response->format = Response::FORMAT_JSON;

        //地区
        $region = $param['region'];
        $regionName = Area::findOne($region);

        if(!empty($param['date']) && $region != '#'){
            //时间范围
            $dateArray = explode(" ",$param['date']);
            //时间
            $dateStart = strtotime(date($dateArray[0]));
            $dateEnd = strtotime(date($dateArray[2]));
            //地区
            $addr = (new Query())
                ->select('id')
                ->from('agentaddr')
                ->where(['like','regionID',substr($param['region'],0,2)])//根据省份前两位确定
                ->all();
            //var_dump($addrID);
            //转化为数组
            foreach ($addr as $key => $value){
                $addrID['id'][$key] = $value['id'];
            }
            $where = ['and',['status' => 3],['and',['between','updateAt',$dateStart,$dateEnd],['in','agentAddrID',$addrID['id']]]];

        } elseif(!empty($param['date']) && $region == '#') {
            //时间不为空,地区为空
            //时间范围
            $dateArray = explode(" ",$param['date']);
            //时间
            $dateStart = strtotime(date($dateArray[0]));
            $dateEnd = strtotime(date($dateArray[2]));
            $where = ['and',['between','updateAt',$dateStart,$dateEnd],['status' => '3']];
            //var_dump('2');
        } elseif($region != '#') {
            //时间为空,地区不为空
            //地区
            $addr = (new Query())
                ->select('id')
                ->from('agentaddr')
                ->where(['like','regionID',substr($param['region'],0,2)])//根据省份前两位确定
                ->all();

            foreach ($addr as $key => $value){
                $addrID['id'][$key] = $value['id'];
            }

            $where = ['and',['in','agentAddrID',$addrID['id']],['status' => '3']];
        } else {
            $where = ['status' => 3];
        }
        $selectResult = AgentOrders::find()->where($where)->orderBy(['addAt' => SORT_DESC])->asArray()->all();

        $status = AgentOrders::getStatus();
        // 拼装参数
        foreach($selectResult as $key => $vo){

            // 状态
            isset($status[$vo['status']]) && $selectResult[$key]['status'] = $status[$vo['status']];
            //时间
            $selectResult[$key]['addAt']=date("Y-m-d H:i:s",$selectResult[$key]['addAt']);
            $selectResult[$key]['updateAt']=date("Y-m-d H:i:s",$selectResult[$key]['updateAt']);

            //地址
            $newAddr = Agentaddr::findOne($selectResult[$key]['agentAddrID']);

            //$addr = Agentaddr::find()->where(['id' =>$info['agentAddrID']])->one();
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
        $PHPExcel->getProperties()->setTitle('代理商订单');
        $PHPExcel->setActiveSheetIndex(0);

        //设置标题
        $PHPExcel->getActiveSheet()->setCellValue('A1',  '代理商订单'.$param['date'] .''.$regionName['Name'] .'');
        //设置居中
        $PHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //合并单元格
        $PHPExcel->getActiveSheet()->mergeCells('A1:G1');

        $PHPExcel->getActiveSheet()->setCellValue("A2",'订单号');
        $PHPExcel->getActiveSheet()->setCellValue("B2",'收货地址');
        $PHPExcel->getActiveSheet()->setCellValue("C2",'总价');
        $PHPExcel->getActiveSheet()->setCellValue("D2",'创建时间');
        $PHPExcel->getActiveSheet()->setCellValue("E2",'更新时间');
        $PHPExcel->getActiveSheet()->setCellValue("F2",'状态');
        $i = 3;

        foreach ($selectResult as $key => $value){
            $PHPExcel->getActiveSheet()->setCellValue("A".$i,$value['ID']);
            $PHPExcel->getActiveSheet()->setCellValue("B".$i,$value['agentAddr']);
            $PHPExcel->getActiveSheet()->setCellValue("C".$i,$value['totalMoney']);
            $PHPExcel->getActiveSheet()->setCellValue("D".$i,$value['addAt']);
            $PHPExcel->getActiveSheet()->setCellValue("E".$i,$value['updateAt']);
            $PHPExcel->getActiveSheet()->setCellValue("F".$i,$value['status']);
            $i++;
        }

        header('Content-Type : application/vnd.ms-excel;charset=utf-8');
        header('Content-Disposition:attachment;filename="代理商订单'.$param['date'].''.$regionName['Name'] .' .xls"');
        $objWriter= \PHPExcel_IOFactory::createWriter($PHPExcel,'Excel5');

        $objWriter->save('php://output');
        exit();
    }
    //代理商图表
    public function actionAgentChart()
    {
        $province = (new Query())
            ->select(['Id','Name'])
            ->from('area')
            ->where(['Pid' => 0])
            ->andWhere(['not in','Id','0'])
            ->all();
        $arrayProvince = ArrayHelper::toArray($province);

        $date = $_GET['date'];
        if(!empty($date)){
            //时间范围
            $dateArray = explode(" ",$date);
            //时间
            $dateStart = strtotime(date($dateArray[0]));
            $dateEnd = strtotime(date($dateArray[2]));

            foreach ($arrayProvince as $key => $value){
                $query = (new Query())
                    ->select('count(*)')
                    ->from('agentorders,agentaddr')
                    //->where(['agentorders.agentAddrID'=>'agentaddr.id'])
                    ->where('agentorders.agentAddrID = agentaddr.id')
                    ->andWhere(['like','agentaddr.regionID',substr($value['Id'],'0','2')])
                    ->andWhere(['between','agentorders.addAt',$dateStart,$dateEnd])
                    ->andWhere('agentorders.status = 3');
                $arrayProvince[$key]['count'] = $query->count();
            }
        } else {
            foreach ($arrayProvince as $key => $value) {
                $query = (new Query())
                    ->select('count(*)')
                    ->from('agentorders,agentaddr')
                    //->where(['agentorders.agentAddrID'=>'agentaddr.id'])
                    ->where('agentorders.agentAddrID = agentaddr.id')
                    ->andWhere(['like', 'agentaddr.regionID', substr($value['Id'], '0', '2')])
                    ->andWhere('agentorders.status = 3');
                $arrayProvince[$key]['count'] = $query->count();
            }
        }
        //var_dump($arrayProvince);

        return $this->render('agent-chart',[
            'arrayProvince' => $arrayProvince,
            'date' => $date
        ]);
    }
    //用户图表
    public function actionUserChart()
    {
        $province = (new Query())
            ->select(['Id','Name'])
            ->from('area')
            ->where(['Pid' => 0])
            ->andWhere(['not in','Id','0'])
            ->all();
        $arrayProvince = ArrayHelper::toArray($province);

        $date = $_GET['date'];
        if(!empty($date)) {
            //时间范围
            $dateArray = explode(" ", $date);
            //时间
            $dateStart = strtotime(date($dateArray[0]));
            $dateEnd = strtotime(date($dateArray[2]));

            foreach ($arrayProvince as $key => $value) {
                $region = substr($value['Id'],'0','2');
                $query = (new Query())
                    ->select('count(*)')
                    ->from('userorders,useraddr')
                    //->where(['userorders.userAddrID'=>'useraddr.id'])
                    ->where('userorders.userAddrID = useraddr.id')
                    //->andWhere(['like', 'useraddr.regionID', substr($value['Id'], '0', '2')])
                    ->andWhere(['like','useraddr.regionID',"$region%",false])
                    ->andWhere(['between', 'userorders.addAt', $dateStart, $dateEnd])
                    ->andWhere('userorders.status = 3');
                $arrayProvince[$key]['count'] = $query->count();
            }
        }else {
                foreach ($arrayProvince as $key => $value){
                    $region = substr($value['Id'],'0','2');
                    $query = (new Query())
                        ->select('count(*)')
                        ->from('userorders,useraddr')
                        //->where(['userorders.userAddrID'=>'useraddr.id'])
                        ->where('userorders.userAddrID = useraddr.id')
                        //->andWhere(['like','useraddr.regionID',substr($value['Id'],'0','2')])
                        ->andWhere(['like','useraddr.regionID',"$region%",false])
                        ->andWhere('userorders.status = 3');
                    $arrayProvince[$key]['count'] = $query->count();
                }
        }



        //var_dump($arrayProvince);

        return $this->render('user-chart',[
            'arrayProvince' => $arrayProvince,
            'date' => $date
        ]);
    }


    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeAgentButton($id)
    {
        return [
            '详情' => [
                'auth' => 'agent-orders/det',
                'href' => "javascript:ordersDet('$id')",
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],
        ];
    }

    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeUserButton($id)
    {
        return [

            '详情' => [
                'auth' => 'uorders/det',
                'href' => "javascript:uordersDet('$id')",
                'btnStyle' => 'info',
                'icon' => 'fa fa-paste'
            ]
        ];
    }


}