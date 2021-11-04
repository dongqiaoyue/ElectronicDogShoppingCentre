<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "dictionary".
 *
 * @property string $ID
 * @property string $Name
 * @property string $Code
 * @property string $parentID
 * @property int $OrderNum
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Dictionary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dictionary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ID', 'Name', 'Code'], 'required'],
            [['addAt'], 'integer'],
            [['ID', 'parentID', 'addBy'], 'string', 'max' => 32],
            [['Name', 'addAgent'], 'string', 'max' => 300],
            [['Code'], 'string', 'max' => 50],
            [['addIP'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'Name' => 'Name',
            'Code' => 'Code',
            'parentID' => 'Parent ID',
            'OrderNum' => 'Order Num',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }
}
