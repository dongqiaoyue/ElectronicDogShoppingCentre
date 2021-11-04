<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "skuingoods".
 *
 * @property string $id
 * @property string $goodsID
 * @property string $ver
 * @property string $skuID
 * @property int $inventory
 * @property string $price
 * @property string $images
 * @property string $content
 * @property int $sort
 * @property string $updateAt
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Skuingoods extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'skuingoods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['ver', 'inventory', 'sort', 'updateAt', 'addAt'], 'integer'],
            [['price'], 'number'],
            [['content'], 'string'],
            [['id', 'goodsID', 'skuID'], 'string', 'max' => 50],
            [['images'], 'string', 'max' => 500],
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
            'goodsID' => 'Goods ID',
            'ver' => 'Ver',
            'skuID' => 'Sku编号',
            'inventory' => '库存',
            'price' => '价格',
            'images' => '图片',
            'content' => 'Content',
            'sort' => 'Sort',
            'updateAt' => 'Update At',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }

    /**
     * 编辑商品sku
     * @param $param
     * @return array
     */
    public function editGoodSku($param)
    {
        try{

            $node = self::findOne($param['id']);
            $node->inventory = $param['inventory'];
            $node->price = $param['price'];
            $node->ver += 1;
            $node->updateAt = $param['updateAt'];
            $node->content = $param['content'];
            $node->attributes = $param;
            $node->save();
            if(false === $node->save()){
                return ['code' => -3, 'data' => $param, 'msg' => array_values($node->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => $param, 'msg' => '编辑商品sku成功'];
    }

    public static function getNewskuID($cart){
        //获取商品最新的版本
        $ver = Goods::getNewById($cart->goodsID);
        $pre_sku = self::find()->where(['id' => $cart->skuID])->one();
        if($ver != $pre_sku->ver){
            $res = self::find()->where(['goodsID' => $pre_sku->goodsID, 'ver' => $ver, 'skuID' => $pre_sku->skuID])->one();
            return $res->id;
        }else{
            return $cart->skuID;
        }

    }
}
