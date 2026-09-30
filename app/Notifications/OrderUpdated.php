<?php

namespace App\Notifications;

use App\Models\Notification;
use App\Models\Order;

class OrderUpdated
{
    public static function create(Order $order)
    {
        Notification::createNotification(
            userId: $order->user_id,
            title: 'Order Updated',
            message: 'Your order #' . $order->id . ' status has been updated to ' . $order->status->value,
            type: 'info',
            actionUrl: url('/orders/' . $order->id)
        );
    }
}