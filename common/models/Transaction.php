<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "transaction".
 *
 * @property string $id
 * @property string $userID
 * @property string $orderID
 * @property int $orderType
 * @property string $Amount
 * @property string $payType
 * @property string $WechatTx
 * @property string $status
 * @property string $memo
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['Amount'], 'number'],
            [['addAt'], 'integer'],
            [['id', 'userID', 'orderID', 'orderType', 'payType', 'WechatTx', 'status'], 'string', 'max' => 50],
            [['memo', 'addAgent'], 'string', 'max' => 300],
            [['addBy'], 'string', 'max' => 32],
            [['addIP'], 'string', 'max' => 100],
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
            'orderID' => 'Order ID',
            'orderType' => 'Order Type',
            'Amount' => 'Amount',
            'payType' => 'Pay Type',
            'WechatTx' => 'Wechat Tx',
            'status' => 'Status',
            'memo' => 'Memo',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }
}
