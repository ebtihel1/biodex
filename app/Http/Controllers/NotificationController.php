<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function read(Request $request, Notification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);

        $notification->markAsRead();

        $context = $request->query('context') === 'back' ? 'back' : 'front';

        return redirect($this->resolveActionUrl($notification->action_url, $context));
    }

    public function readAll()
    {
        Auth::user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return back();
    }

    private function resolveActionUrl(?string $actionUrl, string $context): string
    {
        if (empty($actionUrl)) {
            return url()->previous();
        }

        $path = ltrim(parse_url($actionUrl, PHP_URL_PATH) ?? '', '/');

        if ($context === 'back') {
            $path = preg_replace('#^(biodex/)#', '', $path);
            $frontToBack = [
                'donations'    => 'back/home/donations',
                'orders'       => 'back/home/orders',
                'reservations' => 'back/home/reservations',
            ];
            foreach ($frontToBack as $prefix => $target) {
                if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                    $path = $target . substr($path, strlen($prefix));
                    break;
                }
            }
        } else {
            if (str_starts_with($path, '/')) {
                $path = ltrim($path, '/');
            }
            $frontEntities = ['donations', 'orders', 'reservations', 'cart', 'campaigns'];
            foreach ($frontEntities as $prefix) {
                if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                    $path = 'biodex/' . $path;
                    break;
                }
            }
        }

        return url($path);
    }
}
