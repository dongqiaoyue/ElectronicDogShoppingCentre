<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cart".
 *
 * @property string $id
 * @property string $goodsID
 * @property string $skuID
 * @property int $number
 * @property int $status
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Cart extends \yii\db\ActiveRecord
{
    private static $status = [
        0 => '未处理',
        1 => '已处理',
        2 => '已取消',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cart';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'goodsID'], 'required'],
            [['number', 'status', 'addAt'], 'integer'],
            [['id', 'goodsID', 'skuID', 'addBy'], 'string', 'max' => 32],
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
            'goodsID' => 'Goods ID',
            'skuID' => 'Sku ID',
            'number' => 'Number',
            'status' => 'Status',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }
}
