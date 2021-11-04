<?php

namespace backend\models;

use Yii;
use common\helpers\Tools;

/**
 * This is the model class for table "agentordertrack".
 *
 * @property string $id
 * @property string $orderID
 * @property string $content
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Agentordertrack extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agentordertrack';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['content'], 'string'],
            [['addAt'], 'integer'],
            [['id', 'orderID', 'addBy'], 'string', 'max' => 32],
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
            'orderID' => 'Order ID',
            'content' => 'Content',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }

    //发货
    public function addDeliv($orderID,$trackID)
    {
        try{
            $this->id = Tools::create_id();
            $this->orderID = $orderID;
            $this->content = "发货成功,运单号为:".$trackID;
            $this->addAt = strtotime(date("Y-m-d H:i:s"));

            $order = AgentOrders::findOne($orderID);
            $this->addBy = $order['agentID'];
            $this->addIP = Tools::getClientIp();
            $this->addAgent = Tools::browse_info();
            if(false === $this->save()){
                return ['code' => -3, 'data' =>$trackID, 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '发货成功'];

    }
}
