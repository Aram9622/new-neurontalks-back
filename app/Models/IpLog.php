<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'method',
        'url',
        'referrer',
        'user_agent',
    ];
}
