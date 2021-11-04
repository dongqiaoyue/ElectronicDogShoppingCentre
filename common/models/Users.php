<?php

namespace common\models;

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
 * @property string $openID
 * @property string $unionID
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
            [['id', 'openID', 'unionID', 'addBy'], 'string', 'max' => 32],
            [['phone'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 255],
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
            'openID' => 'Open ID',
            'unionID' => 'Union ID',
        ];
    }
}
