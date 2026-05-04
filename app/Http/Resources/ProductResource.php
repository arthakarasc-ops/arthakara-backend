<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,

            'collection' => $this->collections->name ?? null,
            'type' => $this->types->name ?? null,

            'slug' => $this->slug,
            'price' => (int) $this->price,
            'description' => $this->description,

            'usage_image' => $this->productUsageImages->isNotEmpty()
                ? $this->productUsageImages->first()->image_url
                : null,

            'colors' => $this->whenLoaded('colors'),

            // ✅ VARIANTS (warna)
            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'color' => optional($v->color)->name,
                        'color_hex' => optional($v->color)->hex_code,
                        'stock' => $v->stock ?? 0,
                    ];
                })->values();
            }),

            // ✅ FLAVOR VARIANTS (rasa/wangi - max 2)
            'flavor_variants' => $this->whenLoaded('flavorVariants', function () {
                return $this->flavorVariants->map(function ($fv) {
                    return [
                        'id' => $fv->id,
                        'name' => $fv->name,
                        'type' => $fv->type,
                    ];
                })->values();
            }),

            // ✅ SCENTS
            'scents' => $this->whenLoaded('scents', function () {
                return $this->scents->where('is_active', true)->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'name' => $s->name,
                        'extra_price' => (int) ($s->extra_price ?? 0)
                    ];
                })->values();
            }),

            'created_at' => $this->created_at?->format('d-m-Y'),
        ];
    }
}