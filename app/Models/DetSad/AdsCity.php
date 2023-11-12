<?php

namespace App\Models\DetSad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdsCity extends Model
{
    protected $table = 'i1il4_ads_city';
    protected $primaryKey = 'id';
    protected $fillable = [
        'alias',
        'name'
    ];

    public static function getCity(string $ads_city, string $ads_city_ = '')
    {
        $query = DB::table('i1il4_ads_city')
            ->select('id', 'alias')
            ->where('alias', $ads_city);

        if (!empty($ads_city_)) {
            $query->orWhere($ads_city_);
        }

        return $query->first();
    }

}
