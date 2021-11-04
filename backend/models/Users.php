<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;

/**
 * This is the model class for table "users".
 *
 * @property string $id
 * @property string $phone
 * @property string $password
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'phone'], 'required'],
            [['addAt'], 'integer'],
            [['id', 'addBy'], 'string', 'max' => 32],
            [['phone', 'password'], 'string', 'max' => 255],
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
            'phone' => 'Phone',
            'password' => 'Password',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }

    /**
     * 查询用户的信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getUsersByWhere($where, $offset, $limit)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->andWhere('phone != 1')->offset($offset)->limit($limit)->orderBy(['addAt' => SORT_DESC])->all();
    }

    /**
     * 获取符合条件的用户数量
     * @param $where
     * @return int|string
     */
    public static function getUsersNum($where)
    {
        return self::find()->where($where)->count();
    }

    /**
     * 根据节点id 获取用户信息
     * @param $id
     * @return array
     */
    public static function getUserById($id)
    {
        return self::find()->where(['id' => $id])->one()->toArray();
    }

    /**
     * 删除用户
     * @param $id
     * @return array
     */
    public function delUser($id)
    {
        try{
            $node = self::findOne($id);
            $node->delete();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除用户成功'];
    }

    /**
     * 添加用户
     * @param $param
     * @return array
     */
    public function addUser($param)
    {
        // 检测节点名称的唯一性
        $has = self::find()->select(['id'])->where(['phone' => $param['phone']])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该电话号码已经注册'];
        }
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

        return ['code' => 1, 'data' => '', 'msg' => '添加用户信息成功'];
    }

    /**
     * 批量删除用户信息
     * @param $ids
     * @return array
     */
    public function delUinfoSelected($ids)
    {
        try{
            $condition = 'id in ('. $ids .')';
            self::deleteAll($condition);
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除用户信息成功'];
    }

}
