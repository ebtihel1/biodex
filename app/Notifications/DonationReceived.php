<?php

namespace App\Notifications;

use App\Models\Donation;
use App\Models\Notification;

class DonationReceived
{
    public static function create(Donation $donation)
    {
        Notification::createNotification(
            userId: $donation->user_id,
            title: 'Donation Received',
            message: 'Your donation of ' . $donation->item_name . ' has been received.',
            type: 'success',
            actionUrl: url('/donations/' . $donation->id)
        );
    }
}