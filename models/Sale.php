<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Sale extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%sales}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                // Use UNIX timestamp (bigint fields)
                'value' => function(){ return (int) time(); },
            ],
        ];
    }

    public function rules()
    {
        return [
            [['item','price'], 'required'],
            ['item', 'string', 'max' => 255],
            ['price', 'number'],
            ['description', 'string'],
            ['image', 'string', 'max' => 512],
            ['user_id', 'integer'],
            // user_id is not required in rules because it's always set programmatically in the controller
            ['user_id', 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
