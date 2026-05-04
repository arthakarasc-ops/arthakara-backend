<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'color' => $this->colors ? [
                'id' => $this->colors->id,
                'name' => $this->colors->name,
                'hex' => $this->colors->hex,
            ] : null,
            'fabric' => $this->fabrics ? $this->fabrics->name : null,
            'image_url' => $this->image_url,
            'price' => $this->products ? (int)$this->products->price : null,
            'stock' => $this->stock
        ];
    }
}
