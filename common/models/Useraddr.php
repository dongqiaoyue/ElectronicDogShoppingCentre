<?php

namespace common\models;
use common\helpers\Tools;

use Yii;

/**
 * This is the model class for table "useraddr".
 *
 * @property string $id
 * @property string $userID
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
class Useraddr extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'useraddr';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['status', 'addAt'], 'integer'],
            [['id', 'userID', 'name', 'addBy'], 'string', 'max' => 32],
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
            'userID' => 'User ID',
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

    //添加用户地址
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

    //编辑用户地址
    public function editAddr($param)
    {

        try{
            $node = self::findOne($param['id']);
            //修改默认地址
            if($param['status'] == 1){
                $addr = Useraddr::find()->where(['userID' => $node->userID,'status' => 1])->one();
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
