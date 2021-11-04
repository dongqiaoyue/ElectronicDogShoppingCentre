<?php

namespace backend\models;

use Yii;
use yii\db\Query;
use common\helpers\Tools;

/**
 * This is the model class for table "area".
 *
 * @property string $Id
 * @property string $Name
 * @property string $Pid
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 *
 * @property Area $p
 * @property Area[] $areas
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id'], 'required'],
            [['addAt'], 'integer'],
            [['Id', 'Pid', 'addBy'], 'string', 'max' => 32],
            [['Name'], 'string', 'max' => 50],
            [['addIP'], 'string', 'max' => 100],
            [['addAgent'], 'string', 'max' => 300],
            [['Id'], 'unique','message' => '该地区ID已经被使用'],
            [['Pid'], 'exist', 'skipOnError' => true, 'targetClass' => Area::className(), 'targetAttribute' => ['Pid' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Name' => 'Name',
            'Pid' => 'Pid',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getP()
    {
        return $this->hasOne(self::tableName(), ['Id' => 'Pid']);
    }

    /**
     * 查询地区的信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getAreasByWhere($where, $offset, $limit)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->offset($offset)->limit($limit)->all();
    }

    /**
     * 获取符合条件的地区数量
     * @param $where
     * @return int|string
     */
    public static function getAreaNum($where)
    {
        return self::find()->where($where)->count();
    }

    /**
     * 添加地区
     * @param $param
     * @return array
     */
    public function addArea($param)
    {
        // 检测节点名称的唯一性
        $has = self::find()->select(['Id'])->where(['Name' => $param['Name']])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该地区已经存在'];
        }
        $parent = self::find()->select(['Id'])->where(['Id' => $param['Pid']])->one();
        if(empty($parent)){
            return ['code' => -2, 'data' => '', 'msg' => '不存在该父级ID'];
        }
        if($param['Id'] == $param['Pid']){
            return ['code' => -2, 'data' => '', 'msg' => '地区ID不能和父级ID相同'];
        }
        try{
            $this->attributes = $param;
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '添加地区成功'];
    }

    /**
     * 删除地区
     * @param $id
     * @return array
     */
    public function delArea($id)
    {
        try {

            $node = self::findOne($id);
            $node->delete();
        } catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除地区成功'];
    }

}