<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2018/6/29
 * Time: 16:30
 */
namespace backend\models;

use yii\db\ActiveRecord;
use yii\db\Query;

class Nodes extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rbac_nodes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_menu', 'parentID'], 'integer'],
            [['parentID'], 'required'],
            [['Name', 'auth_rule', 'style'], 'string', 'max' => 155],
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
            'auth_rule' => 'Auth Rule',
            'is_menu' => 'Is Menu',
            'parentID' => 'Parent ID',
            'style' => 'Style',
        ];
    }

    /**
     * 查询节点信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getNodesByWhere($where, $offset, $limit)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->offset($offset)->limit($limit)->all();
    }

    /**
     * 获取符合条件的节点数量
     * @param $where
     * @return int|string
     */
    public static function getNodesNum($where)
    {
        return self::find()->where($where)->count();
    }

    /**
     * 获取所有的节点
     * @return array|ActiveRecord[]
     */
    public static function getAllNodes()
    {
        return (new Query())->from(self::tableName())->all();
    }

    /**
     * 添加节点
     * @param $param
     * @return array
     */
    public function addNodes($param)
    {
        // 检测节点名称的唯一性
        $has = self::find()->select(['ID'])->where(['Name' => $param['Name']])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该节点已经存在'];
        }

        try{

            $this->attributes = $param;
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '添加节点成功'];
    }

    /**
     * 编辑节点
     * @param $param
     * @return array
     */
    public function editNodes($param)
    {
        // 检测节点名称的唯一性
        $has = self::find()->select(['ID'])->where(['Name' => $param['Name']])
            ->andWhere(['<>', 'ID', $param['ID']])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该节点已经存在'];
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

        return ['code' => 1, 'data' => '', 'msg' => '编辑节点成功'];
    }

    /**
     * 删除节点
     * @param $id
     * @return array
     */
    public function delNode($id)
    {
        try{

            $node = self::findOne($id);
            $node->delete();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除节点成功'];
    }

    /**
     * 根据节点id 获取节点信息
     * @param $id
     * @return array
     */
    public static function getNodeById($id)
    {
        return self::find()->where(['ID' => $id])->one()->toArray();
    }
}