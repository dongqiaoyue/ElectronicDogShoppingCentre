<?php

namespace backend\models;

use Yii;
use yii\db\Query;
use common\helpers\Tools;

/**
 * This is the model class for table "dictionary".
 *
 * @property string $ID
 * @property string $Name
 * @property string $Code
 * @property string $parentID
 * @property int $OrderNum
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Dictionary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dictionary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ID', 'Name', 'Code'], 'required'],
            [['addAt'], 'integer'],
            [['ID', 'parentID', 'addBy'], 'string', 'max' => 32],
            [[ 'Code'], 'string', 'max' => 50],
            [['addIP'], 'string', 'max' => 100],
            [['Name','addAgent'], 'string', 'max' => 300],
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
            'Code' => 'Code',
            'parentID' => 'Parent ID',
            'OrderNum' => 'Order Num',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }

    /**
     * 查询字典的信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getDictsByWhere($where, $offset, $limit)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->offset($offset)->limit($limit)->all();
    }

    /**
     * 获取符合条件的字典数量
     * @param $where
     * @return int|string
     */
    public static function getDictsNum($where)
    {
        return self::find()->where($where)->count();
    }

    /**
     * 添加字典
     * @param $param
     * @return array
     */
    public function addDicts($param)
    {
        // 检测节点名称的唯一性
        $has = self::find()->select(['ID'])->where(['Name' => $param['Name']])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该字典已经存在'];
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

        return ['code' => 1, 'data' => '', 'msg' => '添加字典成功'];
    }

    /**
     * 设置代理商优惠
     * @param $param
     * @return array
     */
    public function addDiscount($param)
    {
        // 检测节点名称的唯一性
        //$has = self::find()->select(['ID'])->where(['Code' => $param['id']])->one();
        //编辑
        $dictionary = self::find()->where(['Code' => $param['id']])->one();
        if(!empty($dictionary)){
            $param['addAt'] = time();
            $param['addIP'] = Tools::getClientIp();
            $param['addAgent'] = Tools::browse_info();
            try{
                $dictionary->parentID = "6000";//父级id
                $dictionary->Code = $param['id'];//商品编号
                $dictionary->Name ="";
                foreach ($param['discount'] as $key => $value) {
                    if($param['discount'][$key]!=0 && $param['number'][$key]!=0){
                        $content ="满" . $param['number'][$key] . "个单价为" . $param['discount'][$key] ."元;";
                        $dictionary->Name =$dictionary->Name . $content;
                    }
                }
                $dictionary->attributes = $param;
                if(false === $dictionary->save()){
                    return ['code' => -3, 'data' => $param, 'msg' => array_values($dictionary->errors)['0']['0']];
                }
            }catch (\Exception $e){

                return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
            }

            return ['code' => 1, 'data' => $param, 'msg' => '添加优惠成功'];
        } else {
            $id = Tools::create_id();
            $param['ID'] = $id;
            $param['addAt'] = time();
            $param['addBy'] = $id;
            $param['addIP'] = Tools::getClientIp();
            $param['addAgent'] = Tools::browse_info();
        }
        try{
            $this->parentID = "6000";//父级id
            $this->Code = $param['id'];//商品编号
            $this->Name ="";
            foreach ($param['discount'] as $key => $value) {
                if($param['discount'][$key]!=0 && $param['number'][$key]!=0){
                    $content ="满" . $param['number'][$key] . "个单价为" . $param['discount'][$key] ."元;";
                    $this->Name =$this->Name . $content;
                }
            }

            $this->attributes = $param;
            if(false === $this->save()){
                return ['code' => -3, 'data' => $param, 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => $param, 'msg' => '添加优惠成功'];
    }

    /**
     * 删除代理商优惠
     * @param $param
     * @return array
     */
    public function delDiscount($id)
    {
        try{
            $node = self::find()->where(['Code' => $id])->one();
            //$node->discount="";
            $node->delete();
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除优惠成功'];
    }

    /**
     * 批量删除优惠信息
     * @param $ids
     * @return array
     */
    public function delDiscountSelected($ids)
    {
        try{
            $condition = 'Code in ('. $ids .')';
            Dictionary::deleteAll($condition);
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除优惠信息成功'];
    }


    /**
     * 编辑字典
     * @param $param
     * @return array
     */
    public function editDicts($param)
    {
        // 检测节点名称的唯一性
        $has = self::find()->select(['ID'])->where(['Name' => $param['Name']])
            ->andWhere(['<>', 'ID', $param['ID']])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该字典已经存在'];
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

        return ['code' => 1, 'data' => '', 'msg' => '编辑字典成功'];
    }

    /**
     * 获取所有的字典
     * @return array|ActiveRecord[]
     */
    public static function getAlldicts()
    {
        return (new Query())->from(self::tableName())->all();
    }

    /**
     * 根据字典id 获取字典信息
     * @param $id
     * @return array
     */
    public static function getDictById($id)
    {
        return self::find()->where(['ID' => $id])->one()->toArray();
    }

    /**
     * 删除字典
     * @param $id
     * @return array
     */
    public function delDict($id)
    {
        try{

            $node = self::findOne($id);
            $node->delete();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除字典成功'];
    }

    /**
     * 查询字典信息通过parentID
     */
    public static function getDictByPN($Name)
    {
        $parent = self::find()->where(['Name' => $Name])->one();
        $Pid = $parent->ID;
        $dicts = self::find()->where(['parentID' => $Pid])->all();
        $i = 0;
        $info = [];
        foreach($dicts as $dict) {
            if ($dict->Name != $Name){
                $info[$i][] = $dict->Name;
                $info[$i][] = $dict->Code;
                $i++;
            }
        }

        return $info;
    }

}
