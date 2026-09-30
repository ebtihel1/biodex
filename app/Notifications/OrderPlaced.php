<?php

namespace App\Notifications;

use App\Models\Notification;
use App\Models\Order;

class OrderPlaced
{
    public static function create(Order $order)
    {
        Notification::createNotification(
            userId: $order->user_id,
            title: 'Order Placed Successfully',
            message: 'Your order #' . $order->id . ' for ' . $order->product->name . ' has been placed. Total: $' . $order->total_amount,
            type: 'success',
            actionUrl: url('/orders/' . $order->id)
        );
    }
}