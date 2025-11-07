<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Page extends ActiveRecord
{
    public static function tableName() { return '{{%pages}}'; }

    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::class, 'value'=>function(){return (int)time();}],
        ];
    }

    public function rules()
    {
        return [
            [['slug','content'], 'required'],
            ['slug', 'string', 'max'=>100],
            ['slug', 'unique'],
            ['title', 'string', 'max'=>255],
            ['content', 'string'],
        ];
    }
}
