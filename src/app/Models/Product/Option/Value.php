<?php

namespace App\Models\Product\Option;

use App\Models\Product\Option;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Value extends Model
{
    use HasFactory;

    protected $table = 'product_option_values';

    protected $fillable = ['product_option_id', 'value'];

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'product_option_id');
    }
}
