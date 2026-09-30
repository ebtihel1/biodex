<?php

namespace App\Notifications;

use App\Models\Notification;
use App\Models\Reservation;

class ReservationConfirmed
{
    public static function create(Reservation $reservation)
    {
        Notification::createNotification(
            userId: $reservation->user_id,
            title: 'Reservation Confirmed',
            message: 'Your reservation #' . $reservation->id . ' for ' . $reservation->product->name . ' is confirmed until ' . $reservation->reserved_until->format('Y-m-d'),
            type: 'success',
            actionUrl: url('/reservations/' . $reservation->id)
        );
    }
}