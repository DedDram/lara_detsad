<?php

namespace App\Models\Exchange;

use Illuminate\Database\Eloquent\Model;

class ExchangeMetro extends Model
{
    protected $table = 'i1il4_ads_metro';
    protected $primaryKey = 'id';

    protected $fillable =
        [
            'alias',
            'name',
        ];
}
