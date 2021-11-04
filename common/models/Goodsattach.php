<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "goodsattach".
 *
 * @property string $id
 * @property string $goodsID
 * @property int $ver
 * @property int $type
 * @property string $url
 * @property int $sort
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Goodsattach extends \yii\db\ActiveRecord
{
    // 文件类型
    private static $type = [
        0 => '图片',
        1 => '视频',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'goodsattach';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['ver', 'type', 'sort', 'addAt'], 'integer'],
            [['id', 'goodsID', 'addBy'], 'string', 'max' => 32],
            [['url', 'addAgent'], 'string', 'max' => 300],
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
            'goodsID' => 'Goods ID',
            'ver' => 'Ver',
            'type' => 'Type',
            'url' => 'Url',
            'sort' => 'Sort',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }
}
