<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingAddress extends Model
{
    use HasFactory;

    protected $table = "billing_addresses";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'appartment_suite',
        'city',
        'province',
        'postal_code',
        'country',
        'phone_number'
    ];

    public function orders(): HasMany {
        return $this->hasMany(Order::class, "billing_address_id", "id");
    }
}
