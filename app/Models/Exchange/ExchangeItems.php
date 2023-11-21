<?php

namespace App\Models\Exchange;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExchangeItems extends Model
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
    protected object $city;
    protected object $metro;

    public function __construct(array $attributes = [], int $cityId = 0, string $cityAlias = '', int $metroId = 0, string $metroAlias = '')
    {
        parent::__construct($attributes);
        // Redirect alias
        if(!empty($cityId) || !empty($metroId))
        {
            if(!empty($cityId))
            {
                $this->city = ExchangeCity::selectRaw("CONCAT_WS('-', id, alias) AS alias, name")
                    ->where('id', $cityId)
                    ->first();

                if (empty($this->city->alias)) {
                    abort(404);
                }
            }
            if(!empty($metroId))
            {
                $this->metro = ExchangeMetro::selectRaw("CONCAT_WS('-', id, alias) AS alias, name")
                    ->where('id', $metroId)
                    ->first();

                if (empty($this->metro->alias)) {
                    abort(404);
                }
            }
            if(!empty($this->metro) && ($metroId. '-' .$metroAlias != $this->metro->alias || $cityId. '-' .$cityAlias != $this->city->alias)){
                redirect()->to('/obmen-mest/' . $this->city->alias . '/' . $this->metro->alias)->send();
                exit();
            }
            if(!empty($this->city) &&  $cityId. '-' .$cityAlias != $this->city->alias){
                redirect()->to('/obmen-mest/' . $this->city->alias)->send();
                exit();
            }
        }
    }

    public function getCityName()
    {
        if(!empty($this->city->name))
        {
            return $this->city->name;
        }
    }

    public function getMetroName()
    {
        if(!empty($this->metro->name))
        {
            return $this->metro->name;
        }
    }

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExchangeCity::class, 'city_id');
    }
    public function metro(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExchangeMetro::class, 'metro_id');
    }
    public function teachers(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExchangeTeachers::class, 'teacher_id');
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
        $exchangeItem = ExchangeItems::find($id);
        if($exchangeItem !== null){
            return $exchangeItem->phone;
        }
    }

    public static function getEmail(int $id)
    {
        $exchangeItem = ExchangeItems::find($id);
        if($exchangeItem !== null){
            return $exchangeItem->email;
        }
    }
}
