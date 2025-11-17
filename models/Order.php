<?php
namespace app\models;

use yii\db\ActiveRecord;
use app\models\OrderItem; // import the related model

class Order extends ActiveRecord
{
    public static function tableName()
    {
        return 'orders';
    }

    public function rules()
    {
        return [
            [['user_id', 'total_amount'], 'required'],
            [['user_id'], 'integer'],
            [['total_amount'], 'number'],
            [['status'], 'string'],
        ];
    }

    public function getItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id'])->with('salesItem');
    }
}
