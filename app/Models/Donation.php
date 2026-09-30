<?php

namespace App\Models;

use App\Enums\DonationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'waste_id',
        'item_name',
        'condition',
        'status',
        'description',
        'images',
        'pickup_required',
        'pickup_address'
    ];

    protected $casts = [
        'images' => 'array',
        'pickup_required' => 'boolean',
        'status' => DonationStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function waste()
    {
        return $this->belongsTo(Waste::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', DonationStatus::Available->value);
    }

    public function scopeClaimed($query)
    {
        return $query->where('status', DonationStatus::Claimed->value);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', DonationStatus::Completed->value);
    }
}
