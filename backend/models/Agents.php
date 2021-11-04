<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2018/6/29
 * Time: 16:30
 */
namespace backend\models;
use backend\models\Area;
use yii\data\Sort;
use yii\db\ActiveRecord;
use yii\debug\models\search\Db;
use common\helpers\Tools;

/**
 * This is the model class for table "complaint".
 *
 * @property string $id
 * @property string $name
 * @property string $contactPhone
 * @property string $contactName
 * @property string $region
 * @property string $addr
 * @property int    $status
 * @property string $memo
 * @property string $password
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */


class Agents extends ActiveRecord
{
    // 审核状态
    private static $status = [
        0 => '未审核',
        1 => '已审核'
    ];

    // 审核状态
    private static $Appstatus = [
        0 => '经销商',
        1 => '代理商'
    ];


    // 规则
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['status', 'Appstatus', 'addAt'], 'integer'],
            [['id', 'region', 'addBy'], 'string', 'max' => 32],
            [['name', 'addIP'], 'string', 'max' => 100],
            [['contactPhone', 'contactName'], 'string', 'max' => 20],
            [['addr', 'memo'], 'string', 'max' => 255],
            [['images', 'addAgent'], 'string', 'max' => 300],
            [['password'], 'string', 'max' => 50],
            [['id'], 'unique'],
            [['contactPhone'],'match','pattern' =>'/^1([38][0-9]|4[579]|5[0-3,5-9]|6[6]|7[0135678]|9[89])\d{8}$/','message' => '请输入正确的手机号'],
        ];
    }

    public static function tableName()
    {
        return 'agents';
    }

    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'name' => '公司名称',
            'contactPhone' => '联系方式',
            'contactName' => '联系人',
            'region' => '联系人所在地区',
            'addr' => '详细地址',
            'status'=> '状态',
            'memo'=> '备注',
            'password'=> '密码',
            'addAt' => '添加时间',
            'addBy' => '添加人',
            'addIP' => '添加IP',
            'addAgent' => '添加设备',
        ];
    }


    /**
     * 查询代理人信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getAgentsByWhere($where, $offset, $limit)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->orderBy(['addAt' => SORT_DESC])->offset($offset)->limit($limit)->all();
    }

    /**
     * 获取符合条件的代理人数量
     * @param $where
     * @return int|string
     */
    public static function getAgentNum($where)
    {
        return self::find()->where($where)->count();
    }

    /**
     * 获取代理人审核状态
     * @return int
     */
    public static function getStatus()
    {
        return self::$status;
    }

    /**
     * 获取代理人身份状态
     * @return int
     */
    public static function getAppStatus()
    {
        return self::$Appstatus;
    }

    /**
     * 添加代理商
     * @param $param
     * @return array
     */
    public function addAgents($param)
    {
        // 检测节点名称的唯一性
//        $has = self::find()->select(['id'])->where(['contactName' => $param['name']])->one();
//        if(!empty($has)){
//            return ['code' => -2, 'data' => '', 'msg' => '该代理商已经存在'];
//        }

        $param['addAt'] = time();
        $param['addBy'] = $param['id'];
        $param['addIP'] = Tools::getClientIp();
        $param['addAgent'] = Tools::browse_info();
        try{
            //addAt
            $this->addAt = strtotime(date("Y-m-d H:i:s"));
            //把地区名转化为id
            $areaParent = Area::find()->where(['Name' => $param['city']])->one();
            $area = Area::find()->where(['Name' => $param['region'],'Pid' => $areaParent['Id']])->one();
            $param['region'] = $area->Id;
            $this->attributes = $param;
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '添加代理商成功'];
    }

    /**
     * 编辑代理商信息
     * @param $param
     * @return array
     */
    public function editAgents($param)
    {
        try{
            $node = self::findOne($param['id']);
            //把地区名转化为id
            $areaParent = Area::find()->where(['Name' => $param['city']])->one();
            $area = Area::find()->where(['Name' => $param['region'],'Pid' => $areaParent['Id']])->one();
            $param['region'] = $area->Id;

            $node->attributes = $param;

            if(false === $node->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($node->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }
        //return $historyRes;
        return ['code' => 1, 'data' => '', 'msg' => '编辑代理商信息成功'];
    }

    /**
     * 删除代理商信息
     * @param $id
     * @return array
     */
    public function delAgent($id)
    {
        try{

            $node = self::findOne($id);
            $node->delete();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除代理商信息成功'];
    }

    /**
     * 批量删除代理商信息
     * @param $ids
     * @return array
     */
    public function delAgentSelected($ids)
    {
        try{
            $condition = 'id in ('. $ids .')';
            Agents::deleteAll($condition);
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除代理商信息成功'];
    }

    /**
     * 审核
     * @param $id
     * @return array
     */
    public static function checkAgent($id)
    {
        try{
            $associateRes = self::associateAddr($id);
            if($associateRes['code'] != 1){
                return $associateRes;
            }

            $agent = self::findOne($id);
            $agent->status=1;
            $agent->save();
            $admin = new Admins();
            $res = $admin->addAgents($agent);


        }catch (\Exception $e) {
            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' =>$res, 'msg' => "审核成功"];
    }

    /**
     * 关联已有用户地区
     * @param $id
     * @return array
     */
    public static function associateAddr($id)
    {
        try{
            Agentaddr::deleteAll(['agentID' => $id]);
            $userAddr = Useraddr::findAll(['userID' => $id]);
            //var_dump($userAddr);
            //return $userAddr;
            foreach ($userAddr as $key => $value){
                //$userAddr[$key]['agentID'] = $id;
                $newAddr = new Agentaddr();
                $res = $newAddr->addAddr($value);
                if($res['code'] != 1){
                    return ['code' => -1, 'data' =>$res['msg'], 'msg' => "关联地址失败"];
                }

            }
        }catch (\Exception $e) {
            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' =>'', 'msg' => "关联地址成功"];
    }



    /**
     * 根据节点id 获取代理商信息
     * @param $id
     * @return array
     */
    public static function getAgentById($id)
    {
        return self::find()->where(['id' => $id])->one()->toArray();
    }

}