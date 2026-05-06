<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_id'        => $this->id,
            'status'          => optional($this->statuses)->name,
            'payment_status'  => $this->payment_status,
            'tracking_number' => $this->tracking_number,
            'total_price'     => (int) $this->total_price,
            'created_at'      => $this->created_at->format('d-M-y'),
            'updated_at'      => $this->updated_at->format('d-M-y'),
        ];
    }
}
