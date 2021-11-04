<?php
namespace  console\controllers;
use common\helpers\Tools;
use common\models\Agentorders;
use common\models\Agentordersdetail;
use common\models\Agentordertrack;
use common\models\Skuingoods;
use common\models\Userorders;
use common\models\Userordersdetail;
use common\models\Userordertrack;
use yii\base\Model;

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2019/8/25
 * Time: 19:28
 */

class PayController extends BaseController
{
    /**
     * 释放20分钟内未支付订单的库存
     */
    public function actionUinventory(){
        //前20分钟的时刻
        $beforeDate = strtotime(date('Y-m-d H:i:s', time()-20*60));

        //查询出所有20分钟内未支付的订单
        $canOrders = Userorders::find()
            ->where(['status' => 0])
            ->andWhere(['<=', 'addAt', $beforeDate])
            ->all();

        if(!$canOrders){
            return $this->echoLog('no orders');
        }

        //循环处理订单
        $k = 0;
        foreach ($canOrders as $canOrder){
            //查出需要恢复库存的skuingoods
            $details = Userordersdetail::find()
                ->where(['orderID' => $canOrder->ID])
                ->all();
            foreach($details as $detail){
                //查出对应skuingoods
                $skuGood = Skuingoods::find()
                    ->where(['id' => $detail->skuInGoodsID])
                    ->one();
                //恢复库存
                $skuGood->inventory += $detail->numbers;
                //保存数据
                if(!$skuGood->save()){
                    return $this->echoLog('cancel error');
                }
            }
            //更改订单状态
            $canOrder->status = 4;
            //增加订单跟踪记录
            $track[$k] = new Userordertrack();
            $track[$k]->id = Tools::create_id();
            $track[$k]->orderID = $canOrder->ID;
            $track[$k]->content = '用户取消订单';
            $track[$k]->addAt = time();
//            $track[$k]->addBy = '';
            $track[$k]->addIP = Tools::getClientIp();
            $track[$k]->addAgent = Tools::browse_info();
            //减少库存

            $k++;
        }
        //保存数据
        if(Model::validateMultiple($track)){
            foreach ($track as $key) {
                if (($flag = $key->save(false)) === false) {

                    return $this->echoLog('cancel error');
                }
            }
            foreach ($canOrders as $canOrder){
                if (($flag = $canOrder->save(false)) === false) {

                    return $this->echoLog('cancel error');
                }
            }
        }

        return $this->echoLog('cancel over');
    }

    public function actionAinventory(){
        //前20分钟的时刻
        $beforeDate = strtotime(date('Y-m-d H:i:s', time()-20*60));

        //查询出所有20分钟内未支付的订单
        $canOrders = Agentorders::find()
            ->where(['status' => 0])
            ->andWhere(['<=', 'addAt', $beforeDate])
            ->all();

        if(!$canOrders){
            return $this->echoLog('no orders');
        }

        //循环处理订单
        $k = 0;
        foreach ($canOrders as $canOrder){
            //查出需要恢复库存的skuingoods
            $details = Agentordersdetail::find()
                ->where(['orderID' => $canOrder->ID])
                ->all();
            foreach($details as $detail){
                //查出对应skuingoods
                $skuGood = Skuingoods::find()
                    ->where(['id' => $detail->skuInGoodsID])
                    ->one();
                //恢复库存
                $skuGood->inventory += $detail->numbers;
                //保存数据
                if(!$skuGood->save()){
                    return $this->echoLog('cancel error');
                }
            }
            //更改订单状态
            $canOrder->status = 4;
            //增加订单跟踪记录
            $track[$k] = new Agentordertrack();
            $track[$k]->id = Tools::create_id();
            $track[$k]->orderID = $canOrder->ID;
            $track[$k]->content = '用户取消订单';
            $track[$k]->addAt = time();
//            $track[$k]->addBy = '';
            $track[$k]->addIP = Tools::getClientIp();
            $track[$k]->addAgent = Tools::browse_info();
            //减少库存

            $k++;
        }
        //保存数据
        if(Model::validateMultiple($track)){
            foreach ($track as $key) {
                if (($flag = $key->save(false)) === false) {

                    return $this->echoLog('cancel error');
                }
            }
            foreach ($canOrders as $canOrder){
                if (($flag = $canOrder->save(false)) === false) {

                    return $this->echoLog('cancel error');
                }
            }
        }

        return $this->echoLog('cancel over');
    }
}