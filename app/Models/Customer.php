<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

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
