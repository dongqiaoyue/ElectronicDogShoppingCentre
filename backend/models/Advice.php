<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "advice".
 *
 * @property string $id
 * @property string $name
 * @property string $phone
 * @property string $content
 * @property string $regionID
 * @property string $addr
 * @property int $status
 * @property string $result
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Advice extends \yii\db\ActiveRecord
{
    // 处理状态
    private static $status = [
        0 => '未处理',
        1 => '已处理',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'advice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['content'], 'string'],
            [['status', 'addAt'], 'integer'],
            [['id', 'addBy'], 'string', 'max' => 32],
            [['name', 'addIP'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 20],
            [['regionID', 'addr', 'result'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'phone' => 'Phone',
            'content' => 'Content',
            'regionID' => 'Region ID',
            'addr' => 'Addr',
            'status' => 'Status',
            'result' => 'Result',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }

    /**
     * 查询投诉建议信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return array|ActiveRecord[]
     */
    public static function getAdviceByWhere($where, $offset, $limit)
    {
        return (new \yii\db\Query())->from(self::tableName())->where($where)->orderBy(['addAt' => SORT_DESC])->offset($offset)->limit($limit)->all();
    }

    /**
     * 获取符合条件的投诉信息数量
     * @param $where
     * @return int|string
     */
    public static function getAdviceNum($where)
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
     * 删除投诉信息
     * @param $id
     * @return array
     */
    public function delAdvice($id)
    {
        try{

            $node = self::findOne($id);
            $node->delete();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除建议成功'];
    }

    /**
     * 获取联系人地址
     *
     */
    public static function getAdviceRegionById($id){
        $regionID = self::find()->select(['regionID'])->where(['id' => $id])->one();
        //查询最底层地址
        $last = Area::find()->where(['Id' => $regionID->regionID])->one();
        //查询上一层
        $second = Area::find()->where(['Id' => $last->Pid])->one();
        //判断是否为最高层
        if($second->Pid != '0'){
            $first = Area::find()->where(['Id' => $second->Pid])->one();
            //拼接地址信息
            $addr = $first->Name.$second->Name.$last->Name;
        }else{
            $addr = $second->Name.$last->Name;
        }

        return $addr;
    }

    /**
     * 批量删除代理商信息
     * @param $ids
     * @return array
     */
    public function delAdviceSelected($ids)
    {
        try{
            $condition = 'id in ('. $ids .')';
            Advice::deleteAll($condition);
        }catch (\Exception $e) {

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '删除建议成功'];
    }

    /**
     * 处理投诉信息
     * @param $id
     * @return array
     */
    public static function dealAdvice($id,$result)
    {
        try{
            $advice = self::findOne($id);
            $advice->status = 1;
            $advice->result = $result;
            $advice->save();
        }catch (\Exception $e) {

            return ['code' => -1, 'data' =>$result, 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' =>$result, 'msg' => "处理成功"];
    }

    /**
     * 根据节点id 获取投诉详细信息
     * @param $id
     * @return array
     */
    public static function getAdviceById($id)
    {
        return self::find()->where(['id' => $id])->one()->toArray();
    }
}
