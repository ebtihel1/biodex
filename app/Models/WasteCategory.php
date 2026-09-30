<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'recycling_instructions'];

    // Relations
    public function wastes()
    {
        return $this->hasMany(Waste::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}