<?php

namespace App\Models\Exchange_Job;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExchangeJobItems extends Model
{
    protected $table = 'i1il4_ads_items';
    protected $primaryKey = 'id';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    protected $fillable =
        [
            'city_id',
            'metro_id',
            'teacher_id',
            'type',
            'ip',
            'created',
            'modified',
            'status',
            'hash',
            'fullname',
            'phone',
            'email',
            'photo',
            'text',
            'teach',
        ];


    public function getCity(int $cityId)
    {
        return ExchangeJobCity::selectRaw("CONCAT_WS('-', id, alias) AS alias, name")
            ->where('id', $cityId)
            ->first();
    }

    public function getMetro(int $metroId)
    {
        return ExchangeJobMetro::selectRaw("CONCAT_WS('-', id, alias) AS alias, name")
            ->where('id', $metroId)
            ->first();
    }

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExchangeJobCity::class, 'city_id');
    }
    public function metro(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExchangeJobMetro::class, 'metro_id');
    }
    public function teachers(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExchangeJobTeachers::class, 'teacher_id');
    }

    public function getItems(int $city_id, int $metro_id): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        if(!empty($city_id))
        {
            if(!empty($metro_id))
            {
                return DB::table('i1il4_ads_items AS t1')
                    ->select('t1.*', 't2.name AS city_name', 't3.name AS metro_name',
                        DB::raw("CONCAT_WS('-', t2.id, t2.alias) AS city_alias"),
                        DB::raw("CONCAT_WS('-', t3.id, t3.alias) AS metro_alias"),
                    )
                    ->join('i1il4_ads_city AS t2', 't1.city_id', 't2.id')
                    ->leftJoin('i1il4_ads_metro AS t3', 't1.metro_id', 't3.id')
                    ->where('t1.city_id', $city_id)
                    ->where('t1.metro_id', $metro_id)
                    ->where('t1.type', 0)
                    ->where('t1.status', 1)
                    ->orderBy('t1.id', 'desc')
                    ->paginate(50);
            }else{
                return DB::table('i1il4_ads_items AS t1')
                    ->select('t1.*', 't2.name AS city_name', 't3.name AS metro_name',
                        DB::raw("CONCAT_WS('-', t2.id, t2.alias) AS city_alias"),
                        DB::raw("CONCAT_WS('-', t3.id, t3.alias) AS metro_alias"),
                    )
                    ->join('i1il4_ads_city AS t2', 't1.city_id', 't2.id')
                    ->leftJoin('i1il4_ads_metro AS t3', 't1.metro_id', 't3.id')
                    ->where('t1.city_id', $city_id)
                    ->where('t1.type', 0)
                    ->where('t1.status', 1)
                    ->orderBy('t1.id', 'desc')
                    ->paginate(50);
            }
        }else{
            return DB::table('i1il4_ads_items AS t1')
                ->select('t1.*', 't2.name AS city_name', 't3.name AS metro_name',
                    DB::raw("CONCAT_WS('-', t2.id, t2.alias) AS city_alias"),
                    DB::raw("CONCAT_WS('-', t3.id, t3.alias) AS metro_alias"),
                )
                ->join('i1il4_ads_city AS t2', 't1.city_id', 't2.id')
                ->leftJoin('i1il4_ads_metro AS t3', 't1.metro_id', 't3.id')
                ->where('t1.type', 0)
                ->where('t1.status', 1)
                ->orderBy('t1.id', 'desc')
                ->paginate(50);
        }
    }

    public function getCitySearch(): array
    {
        $cityObjects = DB::table('i1il4_ads_city AS t1')
            ->select('t1.name AS title',
                DB::raw("CONCAT_WS('-', t1.id, t1.alias) AS id"),
            )
            ->join('i1il4_ads_items AS t2', 't1.id', 't2.city_id')
            ->where('t2.type', 0)
            ->groupBy('t1.id', 't1.name', 't1.alias')
            ->orderBy('t1.name', 'asc')
            ->get()
            ->toArray();
        $city = array_map(function ($item) {
            return (array) $item;
        }, $cityObjects);

        array_unshift($city, array('title' => '- Выбрать город -', 'id' => 0));
        return $city;
    }

    public function getMetroSearch(): array
    {
        $metroObjects = DB::table('i1il4_ads_metro AS t1')
            ->select('t1.name AS title',
                DB::raw("CONCAT_WS('-', t1.id, t1.alias) AS id"),
            )
            ->join('i1il4_ads_items AS t2', 't1.id', 't2.metro_id')
            ->where('t2.city_id', 4)
            ->where('t2.type', 0)
            ->groupBy('t1.id', 't1.name', 't1.alias')
            ->orderBy('t1.name', 'asc')
            ->get()
            ->toArray();

        $metro = array_map(function ($item) {
            return (array) $item;
        }, $metroObjects);

        array_unshift($metro, array('title' => '- Станция метро -', 'id' => 0));
        return $metro;
    }

    public static function getPhone(int $id)
    {
        $exchangeItem = ExchangeJobItems::find($id);
        if($exchangeItem !== null){
            return $exchangeItem->phone;
        }
    }

    public static function getEmail(int $id)
    {
        $exchangeItem = ExchangeJobItems::find($id);
        if($exchangeItem !== null){
            return $exchangeItem->email;
        }
    }
}
