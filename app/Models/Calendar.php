<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $table = 'calendar';

    protected $guarded = [];

    protected $casts = [
        'calendar_date' => 'date',
    ];
}
