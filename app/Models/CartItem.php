<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';

    protected $fillable = [
        'user_id',
        'product_variant_id',
        'scents',
        'qty',
        'price'
    ];

    protected $casts = [
        'scents' => 'array',
        'price' => 'integer',
        'qty' => 'integer'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}