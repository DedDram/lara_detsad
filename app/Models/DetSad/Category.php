<?php

namespace App\Models\DetSad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    protected $table = 'i1il4_detsad_categories';
    protected $primaryKey = 'id';
    protected $fillable = ['section_id', 'name', 'alias', 'title', 'text', 'detsad_addresses'];

    public function section(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public static function getCategory(int $categoryId)
    {
        $category = DB::table('i1il4_detsad_categories as t1')
            ->select("t1.*",
                DB::raw("CONCAT_WS('-', t1.id, t1.alias) AS category_alias"),
                DB::raw("CONCAT_WS('-', t2.id, t2.alias) AS section_alias"),
            )
            ->join('i1il4_detsad_sections as t2', 't1.section_id', '=', 't2.id')
            ->where('t1.id', $categoryId)
            ->first();

        if(!empty($category))
        {
            if($category->section_id > 2 && $category->section_id < 12){
                $city_id = 1;
            }else {
                $city_id = $categoryId;
            }
            $category->city = DB::table('i1il4_detsad_categories')
                ->where('detsad_addresses', '>', '100')
                ->where('id', $city_id)
                ->first();
        }
        return $category;
    }
    public static function getDistrict(int $categoryId, string $district)
    {
        return DB::table('i1il4_detsad_districts as t1')
            ->select('t1.id', 't1.name', 't1.morfer_name')
            ->leftJoin('i1il4_detsad_categories as t2', 't1.parent', '=', 't2.name')
            ->where('t1.alias', $district)
            ->where('t2.id', $categoryId)
            ->first();
    }

    public static function getDistricts(int $categoryId): \Illuminate\Support\Collection
    {
        return DB::table('i1il4_detsad_districts as t1')
            ->select('t1.name', 't1.alias')
            ->leftJoin('i1il4_detsad_categories as t2', 't1.parent', '=', 't2.name')
            ->where('t2.id', $categoryId)
            ->orderBy('t1.name')
            ->get();
    }

    public static function getItems(int $categoryId, string $district = ''): \Illuminate\Support\Collection
    {
        if(!empty($district)){
           return DB::table('i1il4_detsad_items as t1')
                ->select(DB::raw('DISTINCT t1.id'), 't1.*', 't2.geo_lat', 't2.geo_long')
                ->join('i1il4_detsad_address as t2', 't1.id', '=', 't2.item_id')
                ->where('t1.category_id', $categoryId)
                ->where('t2.district', $district)
                ->get();
        }else{
            return DB::table('i1il4_detsad_items as t1')
                ->select(DB::raw('DISTINCT t1.id'), 't1.*', 't2.geo_lat', 't2.geo_long')
                ->join('i1il4_detsad_address as t2', 't1.id', '=', 't2.item_id')
                ->where('t1.category_id', $categoryId)
                ->get();
        }
    }

}
