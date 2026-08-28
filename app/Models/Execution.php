<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Execution extends Model
{
    use HasFactory, HasSeo;

    protected $guarded = [];
}
