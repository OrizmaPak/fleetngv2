<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_name',
        'item_cost',
        'item_quantity',
        'driver_id',
    ];

    public function driver()
    {
        return $this->belongsTo('App\Models\Driver','driver_id','id');
    }
}
