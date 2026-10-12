<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function read(Notification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);

        $notification->markAsRead();

        return redirect($notification->action_url ?: url()->previous());
    }

    public function readAll()
    {
        Auth::user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return back();
    }
}
