<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'address',
        'city',
        'google_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relations
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function wastes()
    {
        return $this->hasMany(Waste::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    // NEW: Add this to fix the reservations pluck() error
    public function reservations()
    {
        return $this->hasMany(Reservation::class);  // Assumes foreign key 'user_id'; adjust if e.g., 'customer_id'
        // If model is Booking: return $this->hasMany(Booking::class);
    }

    // Keep bookings() if you use it elsewhere; otherwise, remove if redundant
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Méthode helper pour vérifier le rôle (with null safety)
    public function hasRole($roleName)
    {
        return optional($this->role)->name === $roleName;
    }

    public function initials(): string
    {
        if (empty($this->name)) {
            return 'U'; // U pour "User" si pas de nom
        }

        $words = preg_split('/\s+/', $this->name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return $initials;
    }
}