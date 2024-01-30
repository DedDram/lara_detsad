<?php

namespace App\Models\DetSad;

use App\Models\Comments\Comments;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use ReCaptcha\ReCaptcha;

class Item extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

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
        'okrug',
        'rate',
        'vote',
        'average',
        'comments',
        'nearby',
        'user_id_agent',
        'count_img',
        'sid'
    ];

    public function image(): HasMany
    {
        return $this->hasMany(DetsadImage::class,  'item_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class,  'user_id_agent');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class, 'object_id')->where('object_group', 'com_detsad');
    }

    public function getItem(int $sadId): ?object
    {
        return DB::table('i1il4_detsad_items AS t1')
            ->select(
                't1.*',
                't2.alias as ads_city',
                't2.name as category_name',
                't3.name as section_name',
                DB::raw('CONCAT_WS("-", t1.id, t1.alias) AS item_alias'),
                DB::raw('CONCAT_WS("-", t2.id, t2.alias) AS category_alias'),
                DB::raw('CONCAT_WS("-", t3.id, t3.alias) AS section_alias')
            )
            ->join('i1il4_detsad_categories AS t2', 't1.category_id', 't2.id')
            ->join('i1il4_detsad_sections AS t3', 't1.section_id', 't3.id')
            ->where('t1.id', $sadId)
            ->first();
    }

    public static function getAddress(int $sectionId, string $sectionAlias, int $categoryId, string $categoryAlias, string $sectionName, string $categoryName, int $sadId): object
    {
        if ($sectionId > 1 && $sectionId < 12) {
            $addresses = DB::table('i1il4_detsad_address AS address')
                ->select('address.*', 'items.section_id AS section', 'items.category_id AS cat', 'categories.name AS cat_name', 'categories.alias AS cat_alias', 'sections.name AS section_name', 'sections.alias AS section_alias')
                ->leftJoin('i1il4_detsad_items AS items', 'address.item_id', '=', 'items.id')
                ->leftJoin('i1il4_detsad_categories AS categories', 'categories.id', '=', 'items.category_id')
                ->leftJoin('i1il4_detsad_sections AS sections', 'sections.id', '=', 'items.section_id')
                ->where('address.item_id', $sadId)
                ->get();

        } else {
            $addresses = DB::table('i1il4_detsad_address AS address')
                ->select('address.*', 'items.section_id AS section', 'items.category_id AS cat', 'categories.name AS cat_name', 'categories.alias AS cat_alias', 'sections.name AS section_name', 'sections.alias AS section_alias', 'districts.name AS d_name')
                ->leftJoin('i1il4_detsad_items AS items', 'address.item_id', '=', 'items.id')
                ->leftJoin('i1il4_detsad_categories AS categories', 'categories.id', '=', 'items.category_id')
                ->leftJoin('i1il4_detsad_sections AS sections', 'sections.id', '=', 'items.section_id')
                ->leftJoin('i1il4_detsad_districts AS districts', function ($join) {
                $join->on('districts.alias', '=', 'address.district')->where('districts.parent', '=', DB::raw('address.locality'));
            })
                ->where('address.item_id', $sadId)
                ->get();
        }

        foreach ($addresses as $address) {
            $address->district_link = self::generateDistrictLink($sectionId, $sectionAlias, $categoryId, $categoryAlias, $sectionName, $categoryName, $address);
        }

        return $addresses;
    }

    public static function AjaxMapGeoShow(float $geo_lat, float $geo_long): array
    {
        $result = array();

        $address = DB::table('i1il4_detsad_items AS t1')
            ->select(
                't1.id', 't1.name', 't2.title AS category_title', 't4.geo_lat', 't4.geo_long',
                DB::raw('CONCAT_WS("-", t1.id, t1.alias) AS item_alias'),
                DB::raw('CONCAT_WS("-", t2.id, t2.alias) AS category_alias'),
                DB::raw('CONCAT_WS("-", t3.id, t3.alias) AS section_alias')
            )
            ->join('i1il4_detsad_categories AS t2', 't1.category_id', 't2.id')
            ->join('i1il4_detsad_sections AS t3', 't1.section_id', 't3.id')
            ->join('i1il4_detsad_address AS t4', 't1.id', 't4.item_id')
            ->whereBetween('t4.geo_long', [DB::raw($geo_long - 0.01), DB::raw($geo_long + 0.01)])
            ->whereBetween('t4.geo_lat', [DB::raw($geo_lat - 0.01), DB::raw($geo_lat + 0.01)])
            ->orderBy('t2.id')
            ->orderBy('t1.name')
            ->limit(300)
            ->get();

        if(!empty($address))
        {
            foreach($address as $item)
            {
                $result[] = array(
                    'item_id' => $item->id,
                    'geo_lat' => $item->geo_lat,
                    'geo_long' => $item->geo_long,
                    'url' => env('APP_URL').'/'.$item->section_alias.'/'.$item->category_alias.'/'.$item->item_alias,
                    'geo_code' => $item->name,
                    'category_title' => $item->category_title
                );
            }
        }

        return $result;
    }

    public static function getStatistics(int $sadId): ?object
    {
        return DB::table('i1il4_detsad_stat')->where('item_id',  $sadId)->first();
    }

    public static function getFields(int $sadId): ?object
    {
        return DB::table('i1il4_detsad_fields_value AS t1')
            ->select('t1.item_id', 't1.text AS field_text', 't1.type_id', 't2.text AS type_text')
            ->join('i1il4_detsad_fields_type AS t2', 't1.type_id', '=', 't2.id')
            ->where('t1.item_id', $sadId)
            ->orderBy('t2.ordering', 'ASC')
            ->get();
    }

    public static function getUrlSadik(int $sadId): ?object
    {
        return DB::table('i1il4_detsad_items as t1')
            ->select(DB::raw("CONCAT('/', t3.id, '-', t3.alias, '/', t2.id, '-', t2.alias, '/', t1.id, '-', t1.alias) as url"), "t1.name")
            ->join('i1il4_detsad_categories as t2', 't2.id',  't1.category_id')
            ->join('i1il4_detsad_sections as t3', 't3.id', 't1.section_id')
            ->where('t1.id', $sadId)
            ->first();
    }

    public function getGallery(int $sadId): ?object
    {
        return DetsadImage::where('item_id', $sadId)
            ->where('verified', '1')
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getCountImage(int $sadId): ?int
    {
        return DetsadImage::with('sadik')->where('item_id', $sadId)->count();
    }

    public static function showTelephoneOrEmail($request): array
    {
        $recaptcha = new ReCaptcha(env('APP_ReCaptcha'));

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

    public function getAgent(int $sad_id): ?object
    {
        return User::where('sad_id', $sad_id)->first();
    }

    private static function generateDistrictLink(int $sectionId, string $sectionAlias, int $categoryId, string $categoryAlias, string $sectionName, string $categoryName, object $address): string
    {
        $districtLink = '';

        if ($sectionId > 1 && $sectionId < 12) {
            $districtLink = '<a href="/' . $sectionId . '-' . $sectionAlias . '">' . $sectionName . '</a> / <a href="/' . $sectionId . '-' . $sectionAlias . '/' . $categoryId . '-' . $categoryAlias . '">' . $categoryName . '</a>';
        } elseif ($address->district) {
            $districtLink = '<a href="/' . $sectionId . '-' . $sectionAlias . '/' . $categoryId . '-' . $categoryAlias . '/' . $address->district . '">' . $address->d_name . '</a>';
        }

        return $districtLink;
    }
}
