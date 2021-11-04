<?php

namespace backend\models;

use backend\models\Goodshistory;
use common\helpers\Tools;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

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
class Goods extends ActiveRecord implements Linkable
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

    public function fields()
    {
        return [
            'id',
            'title',
            'content',
            'status',
            'memo',
            'sort',
        ];
    }

//    //用于添加关联字段
//    public function extraFields()
//    {
//        return ['addAt'];
//    }

//    用于添加链接字段
    public function getLinks()
    {
           return [];
    }


    /**
     * 查询商品信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getGoodsByWhere($where)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->orderBy(['sort' => SORT_ASC])->all();
    }

    /**
     * 获取符合条件的商品数量
     * @param $where
     * @return int|string
     */
    public static function getGoodNum($where)
    {
        return self::find()->where($where)->count();
    }

    /**
     * 获取商品上架状态
     * @return int
     */
    public static function getStatus()
    {
        return self::$status;
    }

    /**
     * 添加商品
     * @param $param
     * @return array
     */
    public function addGoods($param)
    {
        // 检测节点名称的唯一性
        $has = self::find()->select(['id'])->where(['title' => $param['title']])
            ->andWhere(['<>', 'id', $param['id']])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该商品已经存在'];
        }
        try{
            //addAt
            $this->addAt = strtotime(date("Y-m-d H:i:s"));
            $this->ver = "1";
            $this->status = "0";
            $this->attributes = $param;
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '添加商品成功'];
    }

    /**
     * 编辑商品
     * @param $param
     * @return array
     */
    public function editGoods($param)
    {
        try{
            $node = self::findOne($param);
            //存历史
            $goodHistory = new Goodshistory();
            $historyRes = $goodHistory->addGoodHistory($node);
            //$goodHistory->attributes = ;
            $goodHistory->save();

            $node->ver +=1;
            $node->attributes = $param;

            if(false === $node->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($node->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }
        //return $historyRes;
        return ['code' => 1, 'data' => '', 'msg' => '编辑商品信息成功'];
    }

    /**
     * 删除商品信息
     * @param $id
     * @return array
     */
    public function delGood($id)
    {
        try{

            $node = self::findOne($id);
            $node->delete();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除商品信息成功'];
    }

    /**
     * 批量删除商品信息
     * @param $ids
     * @return array
     */
    public function delGoodSelected($ids)
    {
        try{
            $condition = 'id in ('. $ids .')';
            Goods::deleteAll($condition);
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除商品信息成功'];
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
                //$good->save();
                if(true == $good->save())
                    return ['code' => 1, 'data' =>'', 'msg' => "上架成功"];
            }

        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' =>'', 'msg' => "成功"];
    }

    /**
     * 根据节点id 获取商品信息
     * @param $id
     * @return array
     */
    public static function getGoodById($id)
    {
        return self::find()->where(['id' => $id])->one()->toArray();
    }
}
