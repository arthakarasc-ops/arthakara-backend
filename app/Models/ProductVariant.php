<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'color_id',
        'image_url',
        'stock',
    ];

    /*
    |----------------------------------------
    | RELATIONSHIPS
    |----------------------------------------
    */

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_variant_id');
    }
}