<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "agentordersdetail".
 *
 * @property string $id
 * @property string $orderID
 * @property string $skuInGoodsID
 * @property int $numbers
 * @property string $price
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Agentordersdetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agentordersdetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['numbers', 'addAt'], 'integer'],
            [['price'], 'number'],
            [['id', 'orderID', 'skuInGoodsID', 'addBy'], 'string', 'max' => 32],
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
            'skuInGoodsID' => 'Sku In Goods ID',
            'numbers' => 'Numbers',
            'price' => 'Price',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }
}
