<?php

namespace App\Models\Exchange_Job;

use Illuminate\Database\Eloquent\Model;

class ExchangeJobCity extends Model
{
    protected $table = 'i1il4_ads_city';
    protected $primaryKey = 'id';

    protected $fillable =
        [
            'alias',
            'name',
        ];
}
