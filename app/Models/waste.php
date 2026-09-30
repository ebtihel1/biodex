<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waste extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'weight',
        'status',
        'user_id',
        'waste_category_id',
        'collection_point_id',
        'image_path',
        'description'
    ];

    protected $casts = [
        'weight' => 'decimal:2',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(WasteCategory::class, 'waste_category_id');
    }

    public function collectionPoint()
    {
        return $this->belongsTo(CollectionPoint::class);
    }

    public function recyclingProcess()
    {
        return $this->hasOne(RecyclingProcess::class);
    }

    public function donation()
    {
        return $this->hasOne(Donation::class);
    }

    // Scopes pour faciliter les requêtes
    public function scopeRecyclable($query)
    {
        return $query->where('status', 'recyclable');
    }

    public function scopeReusable($query)
    {
        return $query->where('status', 'reusable');
    }
}