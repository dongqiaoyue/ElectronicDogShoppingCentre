<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\helpers\Tools;
use yii\db\Query;

/**
 * This is the model class for table "agentaddr".
 *
 * @property string $id
 * @property string $agentID
 * @property string $name
 * @property string $phone
 * @property string $regionID
 * @property string $addr
 * @property int $status
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Agentaddr extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agentaddr';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['status', 'addAt'], 'integer'],
            [['id', 'agentID', 'name', 'addBy'], 'string', 'max' => 32],
            [['phone'], 'string', 'max' => 20],
            [['regionID', 'addr'], 'string', 'max' => 255],
            [['addIP'], 'string', 'max' => 100],
            [['addAgent'], 'string', 'max' => 300],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agentID' => 'Agent ID',
            'name' => 'Name',
            'phone' => 'Phone',
            'regionID' => 'Region ID',
            'addr' => 'Addr',
            'status' => 'Status',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }

    //添加代理商地址
    public function addAddr($param)
    {
        $param['addAt'] = time();
        $param['addBy'] = $param['id'];
        $param['addIP'] = Tools::getClientIp();
        $param['addAgent'] = Tools::browse_info();
        try{
            //addAt
            $this->addAt = strtotime(date("Y-m-d H:i:s"));
            $this->attributes = $param;
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '添加地址成功'];
    }

    //编辑代理商地址
    public function editAddr($param)
    {

        try{
            $node = self::findOne($param['id']);
            //修改默认地址
            if($param['status'] == 1){
                $addr = Agentaddr::find()->where(['agentID' => $node->agentID,'status' => 1])->one();
                if($addr['id'] != $param['id']){
                    if(!empty($addr)){
                        $addr->status = 0;
                        $addr->save();
                    }
                }
            }

            $node->attributes = $param;
            if(false === $node->save()){
                return ['code' => -3, 'data' => $param, 'msg' => array_values($node->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '修改地址成功'];
    }
}
