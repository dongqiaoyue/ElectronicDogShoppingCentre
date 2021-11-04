<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2018/6/29
 * Time: 16:30
 */
namespace backend\models;

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
            [['content'], 'string'],
            [['status', 'addAt'], 'integer'],
            [['id', 'title', 'addBy'], 'string', 'max' => 32],
            [['name', 'addIP'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 20],
            [['image'], 'string', 'max' => 500],
            [['addAgent'], 'string', 'max' => 300],
            [['result'], 'string', 'max' => 255],
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
     * 查询投诉建议信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getComplaintByWhere($where, $offset, $limit)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->orderBy(['addAt' => SORT_DESC])->offset($offset)->limit($limit)->all();
    }

    /**
     * 获取符合条件的投诉信息数量
     * @param $where
     * @return int|string
     */
    public static function getComplaintNum($where)
    {
        return self::find()->where($where)->count();
    }

    /**
     * 获取投诉信息状态
     * @return int
     */
    public static function getStatus()
    {
        return self::$status;
    }

    /**
     * 获取投诉原因
     * @return int
     */
    public static function getTitle()
    {
        return self::$title;
    }

    /**
     * 删除投诉信息
     * @param $id
     * @return array
     */
    public function delComplaint($id)
    {
        try{

            $node = self::findOne($id);
            $node->delete();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除投诉信息成功'];
    }

    /**
     * 批量删除代理商信息
     * @param $ids
     * @return array
     */
    public function delComplaintSelected($ids)
    {
        try{
            $condition = 'id in ('. $ids .')';
            Complaint::deleteAll($condition);
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除投诉信息成功'];
    }

    /**
     * 处理投诉信息
     * @param $id
     * @return array
     */
    public static function dealComplaint($id,$result)
    {
        try{
            $complaint = self::findOne($id);
            $complaint->status = 1;
            $complaint->result = $result;
            $complaint->save();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' =>$result,$id, 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' =>$result,$id, 'msg' => "处理成功"];
    }

    /**
     * 根据节点id 获取投诉详细信息
     * @param $id
     * @return array
     */
    public static function getComplaintById($id)
    {
        return self::find()->where(['id' => $id])->one()->toArray();
    }

}