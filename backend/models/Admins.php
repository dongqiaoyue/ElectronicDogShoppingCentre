<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2018/6/29
 * Time: 16:30
 */
namespace backend\models;

use yii\db\ActiveRecord;

class Admins extends ActiveRecord
{
    // 状态
    private static $status = [
        1 => '启用',
        2 => '禁用'
    ];

    // 规则
    public function rules()
    {
        return [
            ['admin_name', 'required', 'message' => '管理员名称不能为空'],
            ['password', 'required', 'message' => '管理员密码不能为空'],
            ['role_id', 'number'],
            ['status', 'number'],
        ];
    }

    public static function tableName()
    {
        return 'pay_admins';
    }

    /**
     * 查询管理员的信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getAdminsByWhere($where, $offset, $limit)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->offset($offset)->limit($limit)->all();
    }

    /**
     * 获取符合条件的管理员数量
     * @param $where
     * @return int|string
     */
    public static function getAdminsNum($where)
    {
        return self::find()->where($where)->count();
    }

    /**
     * 获取所有的管理员
     * @return array|ActiveRecord[]
     */
    public static function getAllAdmins()
    {
        return self::find()->all();
    }

    /**
     * 添加管理员
     * @param $param
     * @return array
     */
    public function addAdmins($param)
    {
        // 检测节点名称的唯一性
        $has = self::find()->select(['admin_id'])->where(['admin_name' => $param['admin_name']])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该管理员已经存在'];
        }

        try{
            $this->attributes = $param;
            $this->real_name = $param['real_name'];
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '添加管理员成功'];
    }

    /**
     * 添加代理商
     * @param $param
     * @return array
     */
    public function addAgents($param)
    {
        // 检测节点名称的唯一性
//        $has = self::find()->select(['admin_id'])->where(['admin_name' => $param['admin_name']])->one();
//        if(!empty($has)){
//            return ['code' => -2, 'data' => '', 'msg' => '该代理商已经存在'];
//        }

        try{
            //$this->attributes = $param;
            $this->admin_name= $param['contactName'];
            if(!empty($this->password)){
                $this->password= $param['password'];
            } else {
                $this->password=md5('123456'. \Yii::$app->params['salt']);//默认密码
            }

            $this->agent_id= $param['id'];
            $this->status= $param['status'];
            $this->role_id='2';//代理商身份
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '添加代理商成功'];
    }

    /**
     * 通过审核添加代理商
     * @param $param
     * @return array
     */
    public function addAgentsByCheck($param)
    {
        // 检测节点名称的唯一性
//        $has = self::find()->select(['admin_id'])->where(['admin_name' => $param['admin_name']])->one();
//        if(!empty($has)){
//            return ['code' => -2, 'data' => '', 'msg' => '该代理商已经存在'];
//        }

        try{
            //$this->attributes = $param;
            $this->admin_name= $param['contactName'];
            $this->password= $param['password'];
            $this->agent_id= $param['id'];
            $this->status= $param['status'];
            $this->role_id='2';
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '添加代理商成功'];
    }

    /**
     * 删除代理商
     * @param $id
     * @return array
     */
    public function delAgent($id)
    {
        try{

            $node = self::find()->where(['agent_id' => $id])->one();
            if(!empty($node)){
                $node->delete();
            } else {
                return ['code' => 1, 'data' => '', 'msg' => '删除代理商成功'];
            }

        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除代理商成功'];
    }

    /**
     * 编辑管理员
     * @param $param
     * @return array
     */
    public function editAdmins($param)
    {
        // 检测节点名称的唯一性
        $has = self::find()->select(['admin_id'])->where(['admin_name' => $param['admin_name']])
            ->andWhere(['<>', 'admin_id', $param['admin_id']])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该管理员已经存在'];
        }

        try{

            $node = self::findOne($param['admin_id']);

            $node->real_name = $param['real_name'];
            $node->attributes = $param;

            if(false === $node->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($node->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '编辑管理员成功'];
    }

    /**
     * 删除管理员
     * @param $id
     * @return array
     */
    public function delAdmin($id)
    {
        try{

            $node = self::findOne($id);
            $node->delete();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除管理员成功'];
    }

    /**
     * 根据节点id 获取管理员信息
     * @param $id
     * @return array
     */
    public static function getAdminById($id)
    {
        return self::find()->where(['admin_id' => $id])->one()->toArray();
    }

    /**
     * 获取管理员状态数组
     * @return array
     */
    public static function getStatus()
    {
        return self::$status;
    }
}