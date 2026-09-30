<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'image', 'start_date', 'end_date', 'status', 'user_id'
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
