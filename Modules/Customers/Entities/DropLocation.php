<?php

namespace Modules\Customers\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DropLocation extends Model
{
    use HasFactory;

    protected $fillable = ['location'];

    protected static function newFactory()
    {
        return \Modules\Customers\Database\factories\DropLocationFactory::new();
    }
}
