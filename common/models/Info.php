<?php

namespace common\models;

use Yii;
use common\helpers\Tools;

/**
 * This is the model class for table "info".
 *
 * @property string $id
 * @property string $description
 * @property string $cover
 * @property int $status
 * @property string $attach
 */
class Info extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['description'], 'string'],
            [['status', 'addAt'], 'integer'],
            [['id', 'title', 'addBy'], 'string', 'max' => 32],
            [['cover', 'attach'], 'string', 'max' => 500],
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
            'title' => 'Title',
            'description' => 'Description',
            'cover' => 'Cover',
            'status' => 'Status',
            'attach' => 'Attach',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }
}
