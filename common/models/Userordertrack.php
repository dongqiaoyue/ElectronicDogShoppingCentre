<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "userordertrack".
 *
 * @property string $id
 * @property string $orderID
 * @property string $content
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Userordertrack extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userordertrack';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['addAt'], 'integer'],
            [['id', 'orderID'], 'string', 'max' => 50],
            [['content', 'addAgent'], 'string', 'max' => 300],
            [['addBy'], 'string', 'max' => 32],
            [['addIP'], 'string', 'max' => 100],
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
            'orderID' => 'Order ID',
            'content' => 'Content',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }
}
