<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    protected $table = 'groups';

    protected $guarded = [];

    protected $casts = [];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function standings()
    {
        return $this->hasMany(Standing::class)->orderBy('ranking');
    }

    protected static function booted(): void
    {

        static::addGlobalScope('conference', function (Builder $builder) {

            $builder->where('is_conference', 1);

        });

    }
}
