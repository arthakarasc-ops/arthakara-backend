<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $table = "statuses";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
      'name'  
    ];

    public function orders(): HasMany {
        return $this->hasMany(Status::class, "status_id", "id");
    }

    public function payments(): HasMany {
        return $this->hasMany(Payment::class, "payment_status_id", "id");
    }
}
