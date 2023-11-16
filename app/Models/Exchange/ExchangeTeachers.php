<?php

namespace App\Models\Exchange;

use Illuminate\Database\Eloquent\Model;

class ExchangeTeachers extends Model
{
    protected $table = 'i1il4_ads_teachers';
    protected $primaryKey = 'id';

    protected $fillable =
        [
            'name',
        ];
}
