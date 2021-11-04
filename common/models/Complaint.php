<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2018/6/29
 * Time: 16:30
 */
namespace common\models;

use backend\models\Dictionary;
use common\helpers\Tools;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "complaint".
 *
 * @property string $id
 * @property string $name
 * @property string $phone
 * @property string $title
 * @property string $content
 * @property string $image
 * @property string $status
 * @property int    $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */

class Complaint extends ActiveRecord
{
    // 处理状态
    private static $status = [
        0 => '未处理',
        1 => '已处理',
    ];
    // 投诉原因
    private static $title = [
        0 => '发货太慢',
        1 => '质量问题',
        2 => '其他',
    ];

    // 规则
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['title', 'status', 'addAt'], 'integer'],
            [['content'], 'string'],
            [['id', 'addBy','expectation'], 'string', 'max' => 32],
            [['name', 'addIP'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 20],
            [['image'], 'string', 'max' => 500],
            [['addAgent'], 'string', 'max' => 300],
            [['id'], 'unique'],
        ];
    }

    public static function tableName()
    {
        return 'complaint';
    }

    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'name' => '联系人',
            'phone' => '联系方式',
            'title' => '投诉原因',
            'content' => '投诉内容',
            'image' => '图片',
            'status'=> '状态',
            'addAt' => '添加时间',
            'addBy' => '添加人',
            'addIP' => '添加IP',
            'addAgent' => '添加设备',
        ];
    }

    /**
     * 添加投诉信息
     * @param $param
     * @return array
     */
    public function addComplaints($param)
    {

        $param['addAt'] = time();
        $param['addBy'] = $param['id'];
        $param['addIP'] = Tools::getClientIp();
        $param['addAgent'] = Tools::browse_info();
        try{
            //addAt
            //$this->addAt = strtotime(date("Y-m-d H:i:s"));
            $param['status'] = '0';

            $this->attributes = $param;
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '提交投诉信息成功'];
    }



}