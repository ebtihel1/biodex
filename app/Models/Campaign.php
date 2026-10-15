<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'image', 'start_date', 'end_date', 'status', 'user_id',
        'deadline_registration', 'city', 'region', 'participants_count',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'deadline_registration' => 'date:Y-m-d',
        'participants_count' => 'integer',
    ];

    // Relation avec User (créateur de la campagne)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec Participation
    public function participations()
    {
        return $this->hasMany(Participation::class);
    }
}
