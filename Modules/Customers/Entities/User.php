<?php

namespace Modules\Customers\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [];

    protected static function newFactory()
    {
        return \Modules\Customers\Database\factories\UserFactory::new();
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

    
    public function drivers()
    {
        $drivers = Driver::select('id', 'first_name', 'last_name')->where(['merchant_id' => $this->id])->get();
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
