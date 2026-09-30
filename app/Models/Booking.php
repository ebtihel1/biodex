<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'repair_service_id',
        'booking_date',
        'status',
        'notes',
        'item_description',
        'estimated_cost'
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'estimated_cost' => 'decimal:2',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function repairService()
    {
        return $this->belongsTo(RepairService::class);
    }

    // Scopes pour filtrer par statut
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}