<?php
namespace app\controllers;

use Yii;
use yii\rest\Controller; 
use app\models\Order;
use app\models\OrderItem;
use app\models\Sale;

class OrderController extends Controller
{
    // Create new order
    public function actionCreate()
    {
        $data = Yii::$app->request->post();

        $order = new Order();
        $order->user_id = $data['user_id'];
        $order->total_amount = 0;
        $order->save();

        $total = 0;

        foreach ($data['items'] as $i) {
            $salesItem = Sale::findOne($i['sales_id']);

            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->sales_id = $salesItem->id;
            $orderItem->quantity = $i['quantity'];
            $orderItem->price = $salesItem->price;
            $orderItem->save();

            $total += $salesItem->price * $i['quantity'];
        }

        $order->total_amount = $total;
        $order->save();

        return [
            "message" => "Order created successfully",
            "order_id" => $order->id,
            "total" => $total
        ];
    }

    // Get all orders for a user
    public function actionIndex($user_id)
    {
        return Order::find()
            ->where(['user_id' => $user_id])
            ->with('items.salesItem')
            ->all();
    }
}
