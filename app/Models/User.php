<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'photo',
        'merchant_assigned',
        'merchant_id',
        'merchant_name',
        'merchant_address',   
        'payment_user',  
        'auth_pin',
        'last_active',
        'user_type',
        'vehicle_id',
        'is_active',
        'is_payment_user',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'token',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_type' => 'integer',
        'is_active' => 'boolean',
        'merchant_assigned' => 'integer',
        'email_verified_at' => 'datetime',
    ];

    
    /**
     * Get the user's profile image.
     */
    public function getPhotoAttribute($value)
    {
        if($value){
            return \App\Helpers\Helper::imageUrl($value);
        }
        return null;
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

    public function drivers_ids()
    {
        return \App\Support\StaffAccess::drivers($this)->pluck('id');
    }

    public function getIsPaymentUserAttribute($value)
    {
        return (bool) $value || (int) $this->user_type === 5;
    }


    public function client_ids(){
        $ids = Trip::whereIn('driver_id', $this->drivers_ids())->pluck('client_id');
        return $ids;
    }

    public function merchant_drivers()
    {
        $drivers = \App\Support\StaffAccess::drivers($this)->select('id', 'first_name', 'last_name')->where('is_active', 1)->get();
        return $drivers->map(function($item){
            $lastTrip = Trip::where('driver_id', $item->id)->whereIn('status', [1, 2])->orderByDesc('id')->first(); // get previous new trip or live trip of driver
            $available = true;
            if($lastTrip){
                if(!$lastTrip->clock_in_time || !$lastTrip->clock_out_time){
                   $available = false;
                }
            }
            return ['id' => $item->id, 'name' => $item->full_name, 'is_available' => $available];
        });
    }
}
