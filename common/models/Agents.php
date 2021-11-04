<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2018/6/29
 * Time: 16:30
 */
namespace common\models;
use common\models\Area;
use yii\base\Model;
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
        1 => '已审核',
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
     * 获取代理人审核状态
     * @return int
     */
    public static function getStatus()
    {
        return self::$status;
    }

    public function fields()
    {
        return [
            '联系人' => 'contactName',
            'contactPhone',
            '备注' => 'memo',
            'status' => function($model){
                    //$status = self::getStatus();
                    //return $status[$model->status];
                if($this->status==1){
                    return "已审核";
                } else {
                    return "未审核";
                }
            },
            '地区'=> function($model){
                    $area =Area::findOne($model->region);
                    //return $model->region->name;
                    if(!empty($area))
                    {
                        return $area->Name;
                    } else {
                        return "";
                }

            }
        ];
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
//            //把地区名转化为id
//            if(!empty($param['city'])&&!empty($param['region'])){
//                $areaParent = Area::find()->where(['Name' => $param['city']])->one();
//                $area = Area::find()->where(['Name' => $param['region'],'Pid' => $areaParent['Id']])->one();
//                $param['region'] = $area->Id;
//            } else {
//                //默认
//                $param['region'] = '110100';
//            }
            //状态默认为0
            $param['status'] = '0';

            $this->attributes = $param;
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '添加代理商成功'];
    }


}