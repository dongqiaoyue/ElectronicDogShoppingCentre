<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "agentsgoodsprice".
 *
 * @property string $id
 * @property string $agentID
 * @property string $goodsID
 * @property string $price
 * @property int $ver
 * @property string $updateAt
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 * @property string $memo
 */
class Agentsgoodsprice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agentsgoodsprice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'agentID'], 'required'],
            [['price'], 'number'],
            [['ver', 'updateAt', 'addAt'], 'integer'],
            [['id', 'agentID', 'goodsID'], 'string', 'max' => 50],
            [['addBy'], 'string', 'max' => 32],
            [['addIP'], 'string', 'max' => 100],
            [['addAgent'], 'string', 'max' => 300],
            [['memo'], 'string', 'max' => 255],
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
            'agentID' => 'Agent ID',
            'goodsID' => 'Goods ID',
            'price' => 'Price',
            'ver' => 'Ver',
            'updateAt' => 'Update At',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
            'memo' => 'Memo',
        ];
    }

    /**
     * 获取该商品的最新代理商价格版本
     */
    public static function getVer($id, $goodsID){
        $ver = self::find()->where(['agentID' => $id, 'goodsID' => $goodsID])->orderBy(['ver' => SORT_DESC])->one();
        return isset($ver->ver)?$ver->ver:'';
    }
}
