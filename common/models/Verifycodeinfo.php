<?php

namespace common\models;

use Yii;
use common\helpers\Tools;

/**
 * This is the model class for table "verifycodeinfo".
 *
 * @property string $id
 * @property string $phone
 * @property string $verifyCode
 * @property string $addAt
 */
class Verifycodeinfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'verifycodeinfo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['addAt'], 'integer'],
            [['id'], 'string', 'max' => 32],
            [['phone'], 'string', 'max' => 50],
            [['verifyCode'], 'string', 'max' => 10],
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
            'verifyCode' => 'Verify Code',
            'addAt' => 'Add At',
        ];
    }

    //添加验证码地址
    public function addCode($param)
    {
        $param['addAt'] = time();
        try{
            //addAt
            $this->addAt = strtotime(date("Y-m-d H:i:s"));
            $this->attributes = $param;
            if(false === $this->save()){
                return ['code' => -3, 'data' => '', 'msg' => array_values($this->errors)['0']['0']];
            }
        }catch (\Exception $e){

            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }

        return ['code' => 1, 'data' => '', 'msg' => '添加验证码成功'];
    }
}
