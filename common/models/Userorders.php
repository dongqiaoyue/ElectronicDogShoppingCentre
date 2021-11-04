<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "userorders".
 *
 * @property string $ID
 * @property string $userID
 * @property int $status
 * @property string $memo
 * @property string $userAddrID
 * @property string $trackID
 * @property string $postName
 * @property string $totalMoney
 * @property string $updateAt
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Userorders extends \yii\db\ActiveRecord
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
        return 'userorders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ID', 'status'], 'required'],
            [['status', 'updateAt', 'addAt'], 'integer'],
            [['totalMoney'], 'number'],
            [['ID', 'userID', 'userAddrID', 'addBy'], 'string', 'max' => 32],
            [['memo'], 'string', 'max' => 255],
            [['trackID', 'postName'], 'string', 'max' => 50],
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
            'userID' => 'User ID',
            'status' => 'Status',
            'memo' => 'Memo',
            'userAddrID' => 'User Addr ID',
            'trackID' => 'Track ID',
            'postName' => 'Post Name',
            'totalMoney' => 'TotalMoney',
            'updateAt' => 'Update At',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
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
