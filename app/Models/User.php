<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = "users";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'email',
        'password',
        'full_name',
        'phone_number',
        'nickname',
        'avatar',
        'is_admin'
    ];

    protected $hidden = [
        'password',
    ];

    public function addresses(): HasMany {
        return $this->hasMany(Address::class, "user_id", "id");
    }

    public function orders(): HasMany {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

}
