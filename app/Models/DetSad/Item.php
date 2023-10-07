<?php

namespace App\Models\DetSad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    protected $table = 'i1il4_detsad_items';
    protected $primaryKey = 'id';
    protected $fillable = [
        'category_id',
        'section_id',
        'alias',
        'name',
        'title',
        'keywords',
        'description',
        'header',
        'affiliation',
        'text',
        'preview_src',
        'preview_alt',
        'preview_title',
        'preview_border',
        'rating_plus',
        'rating_minus',
        'rating_sum',
        'okrug',
        'rate',
        'vote',
        'average',
        'comments',
        'nearby',
        'inn',
        'sid'
    ];

    public function addresses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Address::class, 'item_id');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function section(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public static function getAddress($sadId): ?object
    {
        return DB::table('i1il4_detsad_address')
            ->select('*')
            ->where('item_id', '=', $sadId)
            ->orderBy('id')
            ->get();
    }

    public static function getStatistics($sadId): ?object
    {
        return DB::table('i1il4_detsad_stat')->select('*')
            ->where('item_id', '=', $sadId)
            ->orderBy('id')
            ->first();
    }

    public static function getFields($sadId): ?object
    {
        return DB::table('i1il4_detsad_fields_value AS t1')
            ->select('t1.item_id', 't1.text AS field_text', 't1.type_id', 't2.text AS type_text')
            ->join('i1il4_detsad_fields_type AS t2', 't1.type_id', '=', 't2.id')
            ->where('t1.item_id', '=', $sadId)
            ->orderBy('t2.ordering', 'ASC')
            ->get();
    }

    public static function getUrlSadik(int $sadId): ?object
    {
        return DB::table('i1il4_detsad_items as t1')
            ->select(DB::raw("CONCAT('/', t3.id, '-', t3.alias, '/', t2.id, '-', t2.alias, '/', t1.id, '-', t1.alias) as url"), "t1.name")
            ->join('i1il4_detsad_categories as t2', 't2.id', '=', 't1.category_id')
            ->join('i1il4_detsad_sections as t3', 't3.id', '=', 't1.section_id')
            ->where('t1.id', $sadId)
            ->first();
    }

    public static function getSadikAgent(int $sadId): ?object
    {
        return DB::table('users')
            ->select('*')
            ->where('vuz_id', $sadId)
            ->first();
    }

    public static function getGallery(int $sadId): ?object
    {
        return DB::table('i1il4_detsad_images')
            ->select('*')
            ->where('item_id', $sadId)
            ->where('verified', '1')
            ->orderBy('id', 'DESC')
            ->get();
    }

    public static function getCountImage(int $sadId): ?int
    {
        return DB::table('i1il4_detsad_images')
            ->where('item_id', $sadId)
            ->count();
    }
}
