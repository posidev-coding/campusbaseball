<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $guarded = [];

    protected $with = ['record'];

    protected $casts = [
        'logos' => 'array',
    ];

    public function record()
    {
        return $this->hasOne(Record::class)->where('scope', 'overall');
    }
}
