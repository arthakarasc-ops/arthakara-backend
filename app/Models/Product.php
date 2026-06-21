<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'stock',
        'weight',
        'description',
        'collection_id',
    ];

    public function productUsageImages(): HasMany
    {
        return $this->hasMany(ProductUsageImage::class);
    }

    public function collections(): BelongsTo
    {
        return $this->belongsTo(Collection::class, 'collection_id');
    }

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'product_types');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function scents(): BelongsToMany
    {
        return $this->belongsToMany(
            Scent::class,
            'product_scents',
            'product_id',
            'scent_id'
        );
    }
}