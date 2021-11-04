<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2018/6/29
 * Time: 16:30
 */
namespace backend\models;

use yii\db\ActiveRecord;
use yii\db\Query;
use yii\db\QueryExpressionBuilder;

class Roles extends ActiveRecord
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
            ['role_name', 'required', 'message' => '角色名不能为空'],
            ['rule', 'string'],
            ['status', 'number']
        ];
    }

    public static function tableName()
    {
        return 'rbac_roles';
    }

    /**
     * 查询角色信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getRolesByWhere($where, $offset, $limit)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->offset($offset)->limit($limit)->all();
    }

    /**
     * 获取符合条件的角色数量
     * @param $where
     * @return int|string
     */
    public static function getRolesNum($where)
    {
        return self::find()->where($where)->count();
    }

    /**
     * 添加角色
     * @param $param
     * @return array
     */
    public function addRoles($param)
    {
        // 检测角色名称的唯一性
        $has = self::find()->select(['role_id'])->where(['role_name' => $param['role_name']])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该角色已经存在'];
        }

        try{

            $this->attributes = $param;
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '添加角色成功'];
    }

    /**
     * 编辑角色
     * @param $param
     * @return array
     */
    public function editRoles($param)
    {
        // 检测角色名称的唯一性
        $has = self::find()->select(['role_id'])->where(['role_name' => $param['role_name']])
            ->andWhere(['<>', 'role_id', $param['role_id']])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该角色已经存在'];
        }

        try{

            $node = self::findOne($param['role_id']);
            $node->attributes = $param;

            if(false === $node->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($node->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '编辑角色成功'];
    }

    /**
     * 删除角色
     * @param $id
     * @return array
     */
    public function delRole($id)
    {
        // 查询该角色是否应被绑定
        $has = (new Query())->from('pay_admins')->where(['role_id' => $id])->one();
        if(!empty($has)){
            return ['code' => -2, 'data' => '', 'msg' => '该角色已被绑定不可删除'];
        }

        try{

            $role = self::findOne($id);
            $role->delete();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除角色成功'];
    }

    /**
     * 根据角色id 获取角色信息
     * @param $id
     * @return array
     */
    public static function getRoleById($id)
    {
        return self::find()->where(['role_id' => $id])->one()->toArray();
    }

    /**
     * 更新角色权限
     * @param $param
     * @return array
     */
    public static function updateRules($param)
    {
        try{

            $role = self::findOne($param['role_id']);
            $role->rule = $param['rules'];
            $role->save();

        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '分配权限成功'];
    }

    /**
     * 获取除了超级管理员之外的所有角色
     * @return array
     */
    public static function getSystemRoles()
    {
        return (new Query())->from(self::tableName())->where(['<>', 'role_id', 1])->andWhere(['status' => 1])->all();
    }

    /**
     * 获取角色状态
     * @return array
     */
    public static function getStatus()
    {
        return self::$status;
    }
}