<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "advice".
 *
 * @property string $id
 * @property string $name
 * @property string $phone
 * @property string $content
 * @property string $regionID
 * @property string $addr
 * @property int $status
 * @property string $result
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Advice extends \yii\db\ActiveRecord
{
    // 处理状态
    private static $status = [
        0 => '未处理',
        1 => '已处理',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'advice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['content'], 'string'],
            [['status', 'addAt'], 'integer'],
            [['id', 'addBy'], 'string', 'max' => 32],
            [['name', 'addIP'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 20],
            [['regionID', 'addr', 'result'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'phone' => 'Phone',
            'content' => 'Content',
            'regionID' => 'Region ID',
            'addr' => 'Addr',
            'status' => 'Status',
            'result' => 'Result',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }
}
