<?php

namespace Modules\Customers\Entities;

use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    protected $table = 'customer_checkouts';
    protected $guarded = ['id'];
    protected $casts = ['trip_amounts' => 'array', 'amount_minor' => 'integer'];
}
