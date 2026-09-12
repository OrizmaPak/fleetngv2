<?php

namespace Modules\Customers\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $hidden = ['password', 'remember_token'];

    protected $fillable = [
        "id",
        "first_name",
        "last_name",
        "profile_image",
        "email",
        "phone_number",
        "country_code",
        "password",
        "email_verified_at",
        "remember_token",
        "is_active",
        "device_token",
        "deleted_at",
        "created_at",
        "updated_at",
    ];
    
    protected static function newFactory()
    {
        return \Modules\Customers\Database\factories\CustomerFactory::new();
    }

    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name'];

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */

    //  Determine full name of user
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
