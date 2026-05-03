<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Приведение даты ответа к объекту Carbon
    protected $casts = [
        'replied_at' => 'datetime',
    ];
}
