<?php

namespace common\models;

use backend\models\Goodshistory;
use common\models\Skuingoods;
use common\models\Goodsattach;
use common\helpers\Tools;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "goods".
 *
 * @property string $id
 * @property int $ver
 * @property string $title
 * @property string $content
 * @property int $status 1已上架/0未上架
 * @property string $memo
 * @property int $sort
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Goods extends ActiveRecord
{

    // 上架状态
    private static $status = [
        0 => '未上架',
        1 => '已上架',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['ver', 'status', 'sort', 'addAt'], 'integer'],
            [['content'], 'string'],
            [['id', 'addBy'], 'string', 'max' => 32],
            [['title', 'addIP'], 'string', 'max' => 100],
            [['memo'], 'string', 'max' => 500],
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
            'ver' => 'Ver',
            'title' => '标题',
            'content' => '描述',
            'status' => '状态',
            'memo' => '备注',
            'sort' => 'Sort',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }
    //获取最新版本
    public static function getNewById($id){
        return self::find()->where(['id' => $id])->one()->ver;
    }

    /**
     * 获取商品列表
     * @return array|false
     */
    public function fields()
    {
        return [
            'id',
            'title',
            'content',
            'memo',
            'ver',
            //选取sort小的图片
            'url'=> function($model) {
                $image = (new Query())
                    ->select(['url'])
                    ->from('goodsattach')
                    ->where(['goodsID' => $model->id,'type' =>'0'])
                    ->orderBy('sort')
                    ->one();

                return $image['url'];
            },
            //选取价格最低的
            'price'=> function($model) {
                $price = (new yii\db\Query())
                    ->select(['price'])
                    ->from('skuingoods')
                    ->where(['goodsID' => $model->id,'ver' => $model->ver])
                    ->min('price');
                return $price;
            },
        ];
    }

    /**
     * 上/下架
     * @param $id
     * @return array
     */
    public static function checkGood($id)
    {
        try{
            $good = self::findOne($id);
            if($good->status){
                $count = self::find()->where(['status' => '1'])->count();
                if($count>1) {
                    $good->status = 0;
                    if(true == $good->save())
                        return ['code' => 1, 'data' =>'', 'msg' => "下架成功"];
                }else{
                    return ['code' => -1, 'data' =>'', 'msg' => "当前只有一个上架商品,无法下架"];
                }
            }else{
                $good->status=1;
                if(true == $good->save())
                    return ['code' => 1, 'data' =>'', 'msg' => "上架成功"];
            }

        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' =>'', 'msg' => "成功"];
    }
}
