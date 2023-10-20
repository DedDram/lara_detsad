<?php

namespace App\Models\DetSad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use ReCaptcha\ReCaptcha;

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

    public static function getAddress($sectionId, $sectionAlias, $categoryId, $categoryAlias, $sectionName, $categoryName, $sadId)
    {

        if ($sectionId > 1 && $sectionId < 12) {
            $addresses = DB::table('i1il4_detsad_address AS address')
                ->select('address.*')
                ->where('item_id', $sadId)
                ->orderBy('address.id')
                ->get();
            foreach ($addresses as $address) {
                $address->district_link = '<a href="/' . $sectionId . '-' . $sectionAlias . '">' . $sectionName . '</a> / <a href="/' . $sectionId . '-' . $sectionAlias . '/' . $categoryId . '-' . $categoryAlias . '">' . $categoryName . '</a>';
            }
        } else {
            $addresses = DB::table('i1il4_detsad_address AS address')
                ->select('address.*')
                ->leftJoin('i1il4_detsad_districts AS districts', function ($join) {
                    $join->on('districts.alias', '=', 'address.district')
                        ->where('districts.parent', '=', 'address.locality');
                })
                ->where('item_id', $sadId)
                ->orderBy('address.id')
                ->get();

            foreach ($addresses as $address) {
                if ($address->district) {
                    $address->district_link = '<a href="/' . $sectionId . '-' . $sectionAlias . '/' . $categoryId . '-' . $categoryAlias . '/' . $address->district . '">' . $address->d_name . '</a>';
                }
            }
        }
        return $addresses;
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function section(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
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

    public static function getAgent(int $sadId)
    {
        $agent = DB::table('i1il4_detsad_agents')
            ->select('*')
            ->where('item_id', $sadId)
            ->where('verified', '1')
            ->first();

        if ($agent !== null) {
            if (!empty($agent->position) && !empty($agent->name) && !empty($agent->phone)) {
                $agent->info = "<strong>Должность:</strong> " . $agent->position . "<br><strong>ФИО:</strong> " . $agent->name . "<br><strong>Телефон:</strong> " . $agent->phone;
            }
            if (!empty($agent->photo_src)) {
                $agent->img = '<img src="/images/detsad/' . $sadId . '/' . $agent->photo_src . '">';
            } else {
                $agent->img = '<img src="/images/no_avatar.gif">';
            }
        }
        return $agent;
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

    public static function showTelephoneOrEmail($request): array
    {
        $recaptcha = new ReCaptcha('6LdECbcoAAAAAAdNzZnIrlu1Mb-m46jbLx-7Nslo');

        $response = $recaptcha->verify($request->input('code'), $request->ip());

        if ($response->isSuccess()) {
            $row = DB::table('i1il4_detsad_fields_value')
                ->select('text')
                ->where('item_id', $request->input('item_id'))
                ->where('type_id', $request->input('type_id'))
                ->first();

            if (!is_null($row)) {
                return array($row->text);
            }
            return array();
        } else {
            return $response->getErrorCodes();
        }

    }
}
