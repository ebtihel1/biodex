<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecyclingProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'waste_id',
        'method',
        'status',
        'start_date',
        'end_date',
        'output_quantity',
        'output_quality',
        'responsible_user_id',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'output_quantity' => 'decimal:2',
    ];

    // Relations
    public function waste()
    {
        return $this->belongsTo(Waste::class);
    }

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Scopes pour les différents statuts
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}