<?php

namespace App\Models;

use App\Models\Product\Option;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'quantity'];

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }
}
