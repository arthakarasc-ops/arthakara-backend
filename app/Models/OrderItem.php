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
        'total_price'
    ];

    public function productVariants(): BelongsTo {
        return $this->belongsTo(ProductVariant::class, "product_variant_id", "id");
    }

    public function orders(): BelongsTo {
        return $this->belongsTo(Order::class, "order_id", "id");
    }

}
