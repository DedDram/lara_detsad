<?php

namespace App\Models\DetSad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Section extends Model
{
    protected $table = 'i1il4_detsad_sections';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'title', 'alias', 'text'];

    public static function getAddress(int $sectionId, string $sectionAlias): array
    {
        $result = array();
        $address = DB::table('i1il4_detsad_items as t1')
            ->select('t1.*',
                DB::raw("CONCAT_WS('-', t1.id, t1.alias) AS item_alias"),
                DB::raw("CONCAT_WS('-', t2.id, t2.alias) AS category_alias"),
                't3.geo_lat', 't3.geo_long')
            ->join('i1il4_detsad_categories as t2', 't1.category_id', '=', 't2.id')
            ->join('i1il4_detsad_address as t3', 't1.id', '=', 't3.item_id')
            ->where('t1.section_id', $sectionId)
            ->where('t3.geo_lat', '>', '0')
            ->where('t3.geo_long', '>', '0')
            ->limit('10000')
            ->get();

        if(!empty($address))
        {
            foreach($address as $item)
            {
                $result[] = array(
                    'geo_lat' => $item->geo_lat,
                    'geo_long' => $item->geo_long,
                    'url' => '/'.$sectionId.'-'.$sectionAlias.'/'.$item->category_alias.'/'.$item->item_alias,
                    'text' => $item->name
                );
            }
        }
        return $result;
    }
}
