<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'description',
        'price',
        'duration',
        'category',
        'is_available',
        'user_id' // Réparateur
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class); // Le réparateur
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Scope pour les services disponibles
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}