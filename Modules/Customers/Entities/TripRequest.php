<?php

namespace Modules\Customers\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TripRequest extends Model
{
    use HasFactory;

    protected $fillable = ["trip_id", "client_id", "driver_id", "is_read", "status"];
    
    protected static function newFactory()
    {
        return \Modules\Customers\Database\factories\TripRequestFactory::new();
    }
}
