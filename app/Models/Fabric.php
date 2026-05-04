<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fabric extends Model
{
    protected $table = "fabrics";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    public function productVariants(): HasMany {
        return $this->hasMany(ProductVariant::class, "fabric_id", "id");
    }
}
