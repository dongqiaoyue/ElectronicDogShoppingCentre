<?php

namespace backend\models;

use Yii;
use common\helpers\Tools;
use yii\db\Query;

/**
 * This is the model class for table "skubasic".
 *
 * @property string $id
 * @property string $name
 * @property string $parentID
 * @property int $sort
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Skubasic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'skubasic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ID'], 'required'],
            [['sort', 'addAt'], 'integer'],
            [['ID', 'parentID', 'addBy'], 'string', 'max' => 32],
            [['Name'], 'string', 'max' => 50],
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
            'Name' => 'Name',
            'parentID' => 'Parent ID',
            'sort' => 'Sort',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }

    /**
     * 查询管理员的信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getSkusByWhere($where, $offset, $limit)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->offset($offset)->limit($limit)->all();
    }

    /**
     * 获取符合条件的管理员数量
     * @param $where
     * @return int|string
     */
    public static function getSkusNum($where)
    {
        return self::find()->where($where)->count();
    }

    /**
     * 获取所有的字典
     * @return array|ActiveRecord[]
     */
    public static function getAllSku()
    {
        return (new Query())->from(self::tableName())->all();
    }

    /**
     * 获得下级sku
     */
    public static function getSkusBelow($where)
    {
        return (new Query())->from(self::tableName())->where($where)->all();
    }

    /**
     * 添加字典
     * @param $param
     * @return array
     */
    public function addSku($param)
    {
        // 检测节点名称的唯一性
        $has = self::find()->select(['ID'])->where(['Name' => $param['Name']])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该sku已经存在'];
        }
        $id = Tools::create_id();
        $param['ID'] = $id;
        $param['addAt'] = time();
        $param['addBy'] = $id;
        $param['addIP'] = Tools::getClientIp();
        $param['addAgent'] = Tools::browse_info();

        try{

            $this->attributes = $param;
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '添加sku成功'];
    }
//
//    /**
//     * 添加代理商
//     * @param $param
//     * @return array
//     */
//    public function addAgents($param)
//    {
//        // 检测节点名称的唯一性
////        $has = self::find()->select(['admin_id'])->where(['admin_name' => $param['admin_name']])->one();
////        if(!empty($has)){
////            return ['code' => -2, 'data' => '', 'msg' => '该管理员已经存在'];
////        }
//
//        try{
//
//            //$this->attributes = $param;
//            $this->admin_name= $param['contactName'];
//            $this->password= $param['password'];
//            $this->agent_id= $param['id'];
//            $this->status= $param['status'];
//            $this->role_id='2';
//            if(false === $this->save()){
//                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
//            }
//        }catch (\Exception $e){
//
//            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
//        }
//
//        return ['code' => 1, 'data' => '', 'msg' => '添加代理商成功'];
//    }
//
    /**
     * 编辑字典
     * @param $param
     * @return array
     */
    public function editSkus($param)
    {
        // 检测节点名称的唯一性
        $has = self::find()->select(['ID'])->where(['Name' => $param['Name']])
            ->andWhere(['<>', 'ID', $param['ID']])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该sku已经存在'];
        }

        try{

            $node = self::findOne($param['ID']);
            $node->attributes = $param;

            if(false === $node->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($node->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '编辑sku成功'];
    }

    /**
     * 删除管理员
     * @param $id
     * @return array
     */
    public function delSku($id)
    {
        try{

            $node = self::findOne($id);
            $node->delete();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除sku成功'];
    }

    /**
     * 根据字典id 获取字典信息
     * @param $id
     * @return array
     */
    public static function getSkuById($id)
    {
        return self::find()->where(['ID' => $id])->one()->toArray();
    }

    /**
     * 批量删除sku信息
     * @param $ids
     * @return array
     */
    public function delSkuSelected($ids)
    {
        try{
            $condition = 'ID in ('. $ids .')';
            self::deleteAll($condition);
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除sku信息成功'];
    }

//
//    /**
//     * 获取管理员状态数组
//     * @return array
//     */
//    public static function getStatus()
//    {
//        return self::$status;
//    }
}
