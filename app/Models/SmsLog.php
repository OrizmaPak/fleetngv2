<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        "from",
        "to",
        "body",
        "account_id",
        "sms_id",
        "type",
        "is_sent",
        'error_message'
    ];
}
