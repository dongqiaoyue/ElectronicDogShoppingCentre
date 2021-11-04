<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;

/**
 * This is the model class for table "goodshistory".
 *
 * @property string $id
 * @property int $ver
 * @property string $title
 * @property string $content
 * @property string $images
 * @property int $status
 * @property string $memo
 * @property int $sort
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Goodshistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'goodshistory';
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
            [['id'], 'string', 'max' => 32],
            [['title', 'addBy'], 'string', 'max' => 100],
            [['images'], 'string', 'max' => 1000],
            [['memo'], 'string', 'max' => 500],
            [['addIP'], 'string', 'max' => 300],
            [['addAgent'], 'string', 'max' => 255],
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
            'title' => 'Title',
            'content' => 'Content',
            'images' => 'Images',
            'status' => 'Status',
            'memo' => 'Memo',
            'sort' => 'Sort',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }

    /**
     * 添加商品历史
     * @param $param
     * @return array
     */
    public function addGoodHistory($param)
    {
        // 检测节点名称的唯一性
//        $has = self::find()->select(['id'])->where(['title' => $param['title']])
//            ->andWhere(['<>', 'id', $param['id']])->one();
//        if(!empty($has)){
//            return ['code' => -2, 'data' => '', 'msg' => '该商品已经存在'];
//        }
        try{
            //addAt
            $id = Tools::create_id();
            $this->id = $id;
//            $this->addAt = time();
            //$this->price = $param['ver'];
            $this->ver = $param->ver;
            $this->title = $param->title;
            $this->content = $param->content;
            //$this->images = $param->images;
            $this->status = $param->status;
            $this->memo = $param->memo;
            $this->addAt = time();
            $this->addBy = $id;
            $this->addIP = Tools::getClientIp();
            $this->addAgent = Tools::browse_info();
//            $this->addAgent = $param->addAgent;
//            $this->addBy = $param->addBy;
//            $this->addIP = $param->addIP;
//            $this->sort = $param->sort;
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '添加商品历史成功'];
    }
}
