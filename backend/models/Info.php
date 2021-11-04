<?php

namespace backend\models;

use Yii;
use common\helpers\Tools;

/**
 * This is the model class for table "info".
 *
 * @property string $id
 * @property string $description
 * @property string $cover
 * @property int $status
 * @property string $attach
 * @property string $attach_copy
 */
class Info extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['description'], 'string'],
            [['sort', 'addAt'], 'integer'],
            [['id', 'title', 'addBy'], 'string', 'max' => 32],
            [['cover', 'attach', 'attach_copy'], 'string', 'max' => 500],
            [['status'], 'string', 'max' => 10],
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
            'title' => 'Title',
            'description' => 'Description',
            'cover' => 'Cover',
            'status' => 'Status',
            'attach' => 'Attach',
            'attach_copy' => 'Attach Copy',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }

    /**
     * 查询新闻的信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getInfoByWhere($where, $offset, $limit)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->groupBy('status,sort')->offset($offset)->limit($limit)->all();
    }

    /**
     * 获取符合条件的新闻数量
     * @param $where
     * @return int|string
     */
    public static function getInfoNum($where)
    {
        return self::find()->where($where)->count();
    }
//
//    /**
//     * 获取所有的管理员
//     * @return array|ActiveRecord[]
//     */
//    public static function getAllAdmins()
//    {
//        return self::find()->all();
//    }
//
    /**
     * 添加管理员
     * @param $param
     * @return array
     */
    public function addInfo($param)
    {
        // 检测节点名称的唯一性
//        $has = self::find()->select(['id'])->where(['title' => $param['title']])->one();
//        if(!empty($has)){
//            return ['code' => -2, 'data' => '', 'msg' => '该基础信息已经存在'];
//        }
        $id = Tools::create_id();
        $param['id'] = $id;
        $param['addAt'] = time();
        $param['addBy'] = $id;
        $param['addIP'] = Tools::getClientIp();
        $param['addAgent'] = Tools::browse_info();

        try{

            $this->attributes = $param;
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '添加基础信息成功'];
    }

    /**
     * 编辑基础信息
     * @param $param
     * @return array
     */
    public function editInfos($param)
    {
        // 检测节点名称的唯一性
        $has = self::find()->select(['id'])->where(['id' => $param['id']])
            ->andWhere(['<>', 'id', $param['id']])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该基础信息已经存在'];
        }

        try{

            $node = self::findOne($param['id']);
            $node->attributes = $param;

            if(false === $node->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($node->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '编辑基础信息成功'];
    }

    /**
     * 删除新闻
     * @param $id
     * @return array
     */
    public function delInfo($id)
    {
        try{

            $node = self::findOne($id);
            $node->delete();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除新闻成功'];
    }

    /**
     * 根据节点id 获取新闻信息
     * @param $id
     * @return array
     */
    public static function getInfoById($id)
    {
        return self::find()->where(['id' => $id])->one()->toArray();
    }

    /**
     * 批量删除新闻信息
     * @param $ids
     * @return array
     */
    public function delCinfoSelected($ids)
    {
        try{
            $condition = 'id in ('. $ids .')';
            self::deleteAll($condition);
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除新闻信息成功'];
    }

    /**
     * 获取管理员状态数组
     * @return array
     */
    public static function getStatusBystus($status)
    {
        $cates = Dictionary::getDictByPN('基础信息');
        $res = '无类别';
        foreach ($cates as $cate){
            if($cate['1'] == $status){
                $res = $cate['0'];
            }
        }
        return $res;
    }


}
