<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    protected $table = "order_items";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;
    protected $fillable = [
        'order_id',
        'product_variant_id',
        'quantity',
        'price_at_purchase',
        'total_price',
        'scents'
    ];

    protected $casts = [
        'scents'             => 'array',
        'quantity'           => 'integer',
        'total_price'        => 'decimal:2',
        'price_at_purchase'  => 'decimal:2',
        'product_variant_id' => 'integer',
    ];

    public function productVariants(): BelongsTo {
        return $this->belongsTo(ProductVariant::class, "product_variant_id", "id");
    }

    public function orders(): BelongsTo {
        return $this->belongsTo(Order::class, "order_id", "id");
    }

}
