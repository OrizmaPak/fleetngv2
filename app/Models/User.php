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
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
    /**
     * Get the user's profile image.
     */
    public function getPhotoAttribute($value)
    {
        if($value){
            return Storage::disk('s3')->url($value);
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
        $ids = [];
        if($this->user_type === 3){
            $ids = Driver::where(['user_id' => $this->id])->pluck('id');
        }

        if($this->user_type === 4){
            $ids = Driver::where(['merchant_id' => $this->id])->pluck('id');
        }

        if($this->is_payment_user){
            $ids = Driver::where(['merchant_id' => $this->merchant_assigned])->pluck('id');
        }
        
        return $ids;
    }

    public function client_ids(){
        $ids = Trip::whereIn('driver_id', $this->drivers_ids())->pluck('client_id');
        return $ids;
    }

    public function merchant_drivers()
    {
        $drivers = Driver::select('id', 'first_name', 'last_name')->where(['merchant_id' => $this->merchant_assigned])->get();
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
