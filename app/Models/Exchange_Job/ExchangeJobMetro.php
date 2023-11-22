<?php

namespace App\Models\Exchange_Job;

use Illuminate\Database\Eloquent\Model;

class ExchangeJobMetro extends Model
{
    protected $table = 'i1il4_ads_metro';
    protected $primaryKey = 'id';

    protected $fillable =
        [
            'alias',
            'name',
        ];
}
