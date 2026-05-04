<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUsageImage extends Model
{
    protected $table = "product_usage_images";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'product_id',
        'image_url'
    ];

    public function products(): BelongsTo {
        return $this->belongsTo(ProductUsageImage::class, "product_id", "id");
    }
}
