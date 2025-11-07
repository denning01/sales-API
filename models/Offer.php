<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Offer extends ActiveRecord
{
    public static function tableName() { return '{{%offers}}'; }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'value' => function(){ return (int) time(); },
            ],
        ];
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description'], 'string'],
            [['starts_at', 'ends_at'], 'integer'],
            [['image'], 'string', 'max'=>512],
        ];
    }
}
