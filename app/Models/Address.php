<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $table = "addresses";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'is_default',
        'first_name',
        'last_name',
        'address',
        'appartment_suite',
        'city',
        'province',
        'postal_code',
        'country',
        'phone_number',
        'user_id'
    ];

    public function users(): BelongsTo {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}
