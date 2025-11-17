<?php
namespace app\models;

use yii\db\ActiveRecord;
use app\models\Sale; // import the sales model

class OrderItem extends ActiveRecord
{
    public static function tableName()
    {
        return 'order_items';
    }

    public function rules()
    {
        return [
            [['order_id', 'sales_id', 'quantity', 'price'], 'required'],
            [['order_id', 'sales_id', 'quantity'], 'integer'],
            [['price'], 'number'],
        ];
    }

    public function getSalesItem()
    {
        return $this->hasOne(Sale::class, ['id' => 'sales_id']);
    }
}

