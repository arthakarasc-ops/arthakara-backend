<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Scent extends Model
{
    use HasFactory;

    protected $table = 'scents';

    protected $fillable = [
        'name',
        'extra_price',
        'is_active'
    ];

    protected $casts = [
        'extra_price' => 'integer',
        'is_active' => 'boolean'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_scent',
            'scent_id',
            'product_id'
        );
    }
}