<?php

namespace common\models;

use Yii;
use yii\db\Query;

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
            [['Id'], 'unique'],
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
     * @return \yii\db\ActiveQuery
     */
    public static function getParent($where)
    {
        return self::find()->where($where);
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