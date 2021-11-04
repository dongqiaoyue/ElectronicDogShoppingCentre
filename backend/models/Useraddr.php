<?php

namespace backend\models;

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
}
