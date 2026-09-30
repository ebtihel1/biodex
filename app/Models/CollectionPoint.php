<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'address',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'opening_hours',
        'accepted_categories',
        'contact_phone',
        'status'
    ];

    protected $casts = [
        'accepted_categories' => 'array',
        'opening_hours' => 'array',
    ];

    // Relations
    public function wastes()
    {
        return $this->hasMany(Waste::class);
    }

    // Méthode pour vérifier si un type de déchet est accepté
    public function acceptsCategory($categoryId)
    {
        return in_array($categoryId, $this->accepted_categories ?? []);
    }
}