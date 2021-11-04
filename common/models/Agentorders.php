<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "agentorders".
 *
 * @property string $ID
 * @property string $agentID
 * @property int $status
 * @property string $memo
 * @property string $agentAddrID
 * @property string $trackID
 * @property string $totalMoney
 * @property int $totalNum
 * @property string $postName
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Agentorders extends \yii\db\ActiveRecord
{
    public static $status = [
        0 => '待付款',
        1 => '待发货',
        2 => '待收货',
        3 => '已完成',
        4 => '已取消'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agentorders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ID'], 'required'],
            [['status', 'totalNum', 'addAt', 'updateAt'], 'integer'],
            [['totalMoney'], 'number'],
            [['ID', 'agentID', 'agentAddrID', 'addBy'], 'string', 'max' => 32],
            [['memo'], 'string', 'max' => 255],
            [['trackID', 'postName'], 'string', 'max' => 50],
            [['sketch', 'addIP'], 'string', 'max' => 100],
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
            'agentID' => 'Agent ID',
            'status' => 'Status',
            'memo' => 'Memo',
            'agentAddrID' => 'Agent Addr ID',
            'trackID' => 'Track ID',
            'totalMoney' => 'Total Money',
            'totalNum' => 'Total Num',
            'postName' => 'Post Name',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
            'updateAt' => 'Update At',
        ];
    }

    /**
     * 获取用户订单状态数组
     */
    public static function getStatus()
    {
        return self::$status;
    }
}
