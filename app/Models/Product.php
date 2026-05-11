<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'price',
        'stock',
        'description',
        'collection_id',
        'type_id'
    ];

    public function productUsageImages(): HasMany {
        return $this->hasMany(ProductUsageImage::class);
    }

    public function collections(): BelongsTo {
        return $this->belongsTo(Collection::class, 'collection_id');
    }

    public function types(): BelongsTo {
        return $this->belongsTo(Type::class, 'type_id');
    }

    // ✅ VARIANT = warna + stock
    public function variants(): HasMany {
        return $this->hasMany(ProductVariant::class,'product_id');
    }

    // ✅ SCENTS = wangi
    public function scents()
    {
        return $this->belongsToMany(
            Scent::class,
            'product_scents',
            'product_id',
            'scent_id'
        );
    }
}