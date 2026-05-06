<?php

namespace App\Http\Resources;

use App\Models\Scent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $variant = $this->productVariants;
        $product = optional($variant)->product;
        $color   = optional($variant)->color;

        // Ambil nama scent dari JSON array ID
        $scentIds    = is_array($this->scents) ? $this->scents : [];
        $scentNames  = Scent::whereIn('id', $scentIds)->pluck('name')->toArray();

        return [
            'id'                 => $this->id,
            'product_variant_id' => $this->product_variant_id,
            'product_name'       => optional($product)->name,
            'color'              => [
                'id'       => optional($color)->id,
                'name'     => optional($color)->name,
                'hex_code' => optional($color)->hex_code,
            ],
            'scents'             => $scentNames,
            'quantity'           => (int) $this->quantity,
            'price_at_purchase'  => (int) $this->price_at_purchase,
            'total_price'        => (int) $this->total_price,
        ];
    }
}
