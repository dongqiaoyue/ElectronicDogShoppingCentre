<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "goodshistory".
 *
 * @property string $id
 * @property int $ver
 * @property string $title
 * @property string $content
 * @property string $images
 * @property int $status
 * @property string $memo
 * @property int $sort
 * @property string $addAt
 * @property string $addBy
 * @property string $addIP
 * @property string $addAgent
 */
class Goodshistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'goodshistory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['ver', 'status', 'sort', 'addAt'], 'integer'],
            [['content'], 'string'],
            [['id'], 'string', 'max' => 32],
            [['title', 'addBy'], 'string', 'max' => 100],
            [['images'], 'string', 'max' => 1000],
            [['memo'], 'string', 'max' => 500],
            [['addIP'], 'string', 'max' => 300],
            [['addAgent'], 'string', 'max' => 255],
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
            'ver' => 'Ver',
            'title' => 'Title',
            'content' => 'Content',
            'images' => 'Images',
            'status' => 'Status',
            'memo' => 'Memo',
            'sort' => 'Sort',
            'addAt' => 'Add At',
            'addBy' => 'Add By',
            'addIP' => 'Add Ip',
            'addAgent' => 'Add Agent',
        ];
    }
}
