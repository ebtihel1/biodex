<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ReservationStatus;  // Assuming your enum

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'reserved_until',
        'user_id',
        'status',
    ];

    protected $casts = [
        'reserved_until' => 'datetime',
        'status' => ReservationStatus::class,
    ];

    // NEW: Relationship to Product (fixes FK integrity and allows $reservation->product)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}