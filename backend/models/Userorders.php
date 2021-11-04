<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;

/**
 * This is the model class for table "userorders".
 *
 * @property string $ID
 * @property string $userID
 * @property int $status
 * @property string $memo
 * @property string $userAddID
 * @property string $trackID
 * @property string $postName
 * @property string $totalmoney
 * @property string $updateAt
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Userorders extends \yii\db\ActiveRecord
{
    public static $status = [
        0 => '待付款',
        1 => '已付款',
        2 => '已发货',
        3 => '已完成',
        4 => '已取消'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userorders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ID'], 'required'],
            [['status', 'updateAt', 'addAt'], 'integer'],
            [['totalMoney'], 'number'],
            [['ID', 'userID', 'userAddrID', 'addBy'], 'string', 'max' => 32],
            [['memo'], 'string', 'max' => 255],
            [['trackID', 'postName'], 'string', 'max' => 50],
            [['addIP'], 'string', 'max' => 100],
            [['addAgent'], 'string', 'max' => 300],
            [['ID'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'userID' => 'User ID',
            'status' => 'Status',
            'memo' => 'Memo',
            'userAddID' => 'User Add ID',
            'trackID' => 'Track ID',
            'postName' => 'Post Name',
            'totalmoney' => 'Totalmoney',
            'updateAt' => 'Update At',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }

    /**
     * 查询用户订单的信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getUordersByWhere($where, $offset, $limit)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->offset($offset)->limit($limit)->all();
    }

    /**
     * 获取符合条件的用户订单数量
     * @param $where
     * @return int|string
     */
    public static function getUordersNum($where)
    {
        return self::find()->where($where)->count();
    }

    /**
     * 查询订单信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getUserOrdersByWhere($where, $offset, $limit)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->orderBy(['addAt' => SORT_DESC])->offset($offset)->limit($limit)->all();
    }


    /**
     * 批量删除用户订单信息
     * @param $ids
     * @return array
     */
    public function delOrdersSelected($ids)
    {
        try{
            $condition = 'ID in ('. $ids .')';
            Userorders::deleteAll($condition);
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除订单信息成功'];
    }

    /**
     * 完成用户订单信息
     * @param $id
     * @return array
     */
    public function comUserOrders($id)
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
     * 批量完成用户订单信息
     * @param $ids
     * @return array
     */
    public function comOrdersSelected($ids)
    {
        try{
            $condition = 'ID in ('. $ids .')';
            //Userorders::deleteAll($condition);
            Userorders::updateAll(['status' => '3'],$condition);
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '订单已完成'];
    }

//    /**
//     * 获取所有的管理员
//     * @return array|ActiveRecord[]
//     */
//    public static function getAllAdmins()
//    {
//        return self::find()->all();
//
//
//    /**
//     * 编辑管理员
//     * @param $param
//     * @return array
//     */
//    public function editAdmins($param)
//    {
//        // 检测节点名称的唯一性
//        $has = self::find()->select(['admin_id'])->where(['admin_name' => $param['admin_name']])
//            ->andWhere(['<>', 'admin_id', $param['admin_id']])->one();
//        if(!empty($has)){
//            return ['code' => -2, 'data' => '', 'msg' => '该管理员已经存在'];
//        }
//
//        try{
//
//            $node = self::findOne($param['admin_id']);
//            $node->attributes = $param;
//
//            if(false === $node->save()){
//                return ['code' => -3, 'data' => '', 'msg' => array_values($node->errors)['0']['0']];
//            }
//        }catch (\Exception $e){
//
//            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
//        }
//
//        return ['code' => 1, 'data' => '', 'msg' => '编辑管理员成功'];
//    }
//
//    /**
//     * 删除管理员
//     * @param $id
//     * @return array
//     */
//    public function delAdmin($id)
//    {
//        try{
//
//            $node = self::findOne($id);
//            $node->delete();
//        }catch (\Exception $e) {
//
//            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
//        }
//
//        return ['code' => 1, 'data' => '', 'msg' => '删除管理员成功'];
//    }
//
    /**
     * 根据节点id 获取用户订单信息
     * @param $id
     * @return array
     */
    public static function getUordersById($id)
    {
        return self::find()->where(['ID' => $id])->one()->toArray();
    }

    /**
     * 获取用户订单状态数组
     */
    public static function getStatus()
    {
        return self::$status;
    }
    
    //发货
    public function Deliv($id,$trackID)
    {
        $node = Userorders::findOne(['trackID' => $trackID]);
        if(!empty($node)){
            return ['code' => -1, 'data' => '', 'msg' => '运单号重复'];
        }
        try{
            $order = Userorders::findOne($id);
            $order->status = '2';//已发货
            $order->trackID = $trackID;
            $order->updateAt = strtotime(date("Y-m-d H:i:s"));
            $orderTrack = new Userordertrack();
            $trackRes = $orderTrack->addDeliv($id,$trackID);
            if(false === $order->save()){
                return ['code' => -3, 'data' =>$trackID, 'msg' => array_values($this->errors)['0']['0']];
            }
        } catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '发货成功'];

    }

}
