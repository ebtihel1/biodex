<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
        'related_entity_type',
        'related_entity_id',
        'action_url'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope pour les notifications non lues
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // Méthode pour marquer comme lue
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    // Méthode pour créer une notification
    public static function createNotification($userId, $title, $message, $type = 'info', $actionUrl = null)
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
            'action_url' => $actionUrl,
        ]);
    }
}