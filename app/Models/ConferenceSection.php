<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConferenceSection extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
