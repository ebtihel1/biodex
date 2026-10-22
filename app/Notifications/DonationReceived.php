<?php

namespace App\Notifications;

use App\Models\Donation;
use App\Models\Notification;
use App\Models\User;

class DonationReceived
{
    public static function create(Donation $donation)
    {
        Notification::createNotification(
            userId: $donation->user_id,
            title: 'Donation Received',
            message: 'Your donation of ' . $donation->item_name . ' has been received.',
            type: 'success',
            actionUrl: '/biodex/donations/' . $donation->id
        );

        self::notifyBackoffice($donation);
    }

    private static function notifyBackoffice(Donation $donation): void
    {
        $donation->loadMissing(['user', 'waste.category', 'waste.collectionPoint']);

        $backofficeUsers = User::query()
            ->whereHas('role', fn ($query) => $query->whereIn('name', ['admin', 'entreprise']))
            ->where('id', '!=', $donation->user_id)
            ->get();

        foreach ($backofficeUsers as $user) {
            Notification::createNotification(
                userId: $user->id,
                title: 'New Donation ' . $donation->id,
                message: self::backofficeMessage($donation),
                type: 'info',
                actionUrl: route('back.donations.show', $donation, false)
            );
        }
    }

    private static function backofficeMessage(Donation $donation): string
    {
        $lines = [
            'Donor: ' . ($donation->user->name ?? 'Unknown'),
            'Item: ' . ($donation->item_name ?? 'N/A'),
            'Category: ' . ($donation->waste->category->name ?? 'N/A'),
            'Condition: ' . ucfirst($donation->condition ?? 'N/A'),
            'Collection point: ' . ($donation->waste->collectionPoint->name ?? 'N/A'),
        ];

        if ($donation->pickup_required) {
            $lines[] = 'Pickup: Yes' . ($donation->pickup_address ? ' - ' . $donation->pickup_address : '');
        }

        $lines[] = 'Open the notification to view the backoffice donation details.';

        return implode("\n", $lines);
    }
}
