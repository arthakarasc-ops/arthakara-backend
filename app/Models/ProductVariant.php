<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use SoftDeletes;
    protected $table = "product_variants";

    protected $fillable = [
        'product_id',
        'color_id',
        'image_url',
        'stock'
    ];

    /*
    |----------------------------------------
    | RELATIONSHIPS
    |----------------------------------------
    */

    // 🔥 ke Product (FIXED)
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id")->withTrashed();
    }

    // 🔥 ke Color (FIXED)
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, "color_id");
    }

    // 🔥 ke Order Items
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, "product_variant_id");
    }
}