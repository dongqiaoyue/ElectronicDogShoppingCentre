<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2018/6/29
 * Time: 16:30
 */
namespace backend\models;

use yii\db\ActiveRecord;


class AgentOrders extends ActiveRecord
{
    // 审核状态
    private static $status = [
        0 => '待付款',
        1 => '已付款',
        2 => '已发货',
        3 => '已完成',
        4 => '已取消',
    ];



    public static function tableName()
    {
        return 'agentorders';
    }

    public function attributeLabels()
    {
        return [
            'ID' => '编号',
            'agentID' => '代理商编号',
            'agentAddrID' => '收货地址',
            'trackID' => '运单号',
            'totalMoney' => '总金额',
            'postName' => '快递公司',
            'status'=> '状态',
            'memo'=> '备注',
            'addAt' => '添加时间',
            'addBy' => '添加人',
            'addIP' => '添加IP',
            'addAgent' => '添加设备',
        ];
    }


    /**
     * 查询订单信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getAgentOrdersByWhere($where, $offset, $limit)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->orderBy(['addAt' => SORT_DESC])->offset($offset)->limit($limit)->all();
    }

    /**
     * 获取符合条件的订单数量
     * @param $where
     * @return int|string
     */
    public static function getAgentOrdersNum($where)
    {
        return self::find()->where($where)->count();
    }

    /**
     * 获取订单信息状态
     * @return int
     */
    public static function getStatus()
    {
        return self::$status;
    }

    /**
     * 根据节点id 获取订单详细信息
     * @param $id
     * @return array
     */
    public static function getOrdersById($id)
    {
        return self::find()->where(['ID' => $id])->one()->toArray();
    }

    /**
     * 删除代理商订单信息
     * @param $id
     * @return array
     */
    public function delAgentOrders($id)
    {
        try{

            $node = self::findOne($id);
            $node->delete();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除订单信息成功'];
    }

    /**
     * 批量删除代理商订单信息
     * @param $ids
     * @return array
     */
    public function delOrdersSelected($ids)
    {
        try{
            $condition = 'ID in ('. $ids .')';
            AgentOrders::deleteAll($condition);
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除订单信息成功'];
    }

    /**
     * 完成代理商订单信息
     * @param $id
     * @return array
     */
    public function comAgentOrders($id)
    {
        try{

            $node = self::findOne($id);
            $node->status = 3;//完成订单
            $node->save();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '订单已完成'];
    }

    /**
     * 批量完成代理商订单信息
     * @param $ids
     * @return array
     */
    public function comOrdersSelected($ids)
    {
        try{
            $condition = 'ID in ('. $ids .')';
            //AgentOrders::deleteAll($condition);
            AgentOrders::updateAll(['status' => '3'],$condition);
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '订单已完成'];
    }

    //发货
    public function Deliv($id,$trackID)
    {
        $node = AgentOrders::findOne(['trackID' => $trackID]);
        if(!empty($node)){
            return ['code' => -1, 'data' => '', 'msg' => '运单号重复'];
        }
        try{
            $order = AgentOrders::findOne($id);
            $order->status = '2';//已发货
            $order->trackID = $trackID;
            $order->updateAt = strtotime(date("Y-m-d H:i:s"));
            if(false === $order->save()){
                return ['code' => -3, 'data' =>$trackID, 'msg' => array_values($order->errors)['0']['0']];
            }
            $orderTrack = new Agentordertrack();
            $trackRes = $orderTrack->addDeliv($id,$trackID);
        } catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '发货成功'];

    }

}