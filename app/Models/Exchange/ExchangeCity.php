<?php

namespace App\Models\Exchange;

use Illuminate\Database\Eloquent\Model;

class ExchangeCity extends Model
{
    protected $table = 'i1il4_ads_city';
    protected $primaryKey = 'id';

    protected $fillable =
        [
            'alias',
            'name',
        ];
}
