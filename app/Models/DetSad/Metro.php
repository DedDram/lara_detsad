<?php

namespace App\Models\DetSad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Metro extends Model
{
    protected $table = 'i1il4_detsad_metro';
    protected $primaryKey = 'id';
    protected $fillable = ['city', 'name', 'alias', 'geo_long', 'geo_lat'];

    public function getAddress(int $metro_id): array
    {
        $result = array();

        $address = DB::table('i1il4_detsad_items as t1')
            ->select(
                DB::raw("CONCAT_WS('-', t1.id, t1.alias) AS item_alias"),
                DB::raw("CONCAT_WS('-', t2.id, t2.alias) AS category_alias"),
                DB::raw("CONCAT_WS('-', t3.id, t3.alias) AS section_alias"),
                't1.*', 't4.geo_lat', 't4.geo_long', 't5.city', 't5.name as metroName'
            )
            ->leftJoin('i1il4_detsad_categories AS t2', 't1.category_id', 't2.id')
            ->leftJoin('i1il4_detsad_sections AS t3', 't1.section_id', 't3.id')
            ->leftJoin('i1il4_detsad_address AS t4', 't1.id', 't4.item_id')
            ->leftJoin('i1il4_detsad_metro AS t5', DB::raw("CONCAT(',', t4.metro, ',')"), 'LIKE',DB::raw("CONCAT('%,', t5.id,',%')"))
            ->where('t5.id', $metro_id)
            ->get();

        if(!empty($address))
        {
            $n = 1;
            foreach($address  as $item)
            {
                $resultItem = (object)[
                    'geo_lat' => $item->geo_lat,
                    'geo_long' => $item->geo_long,
                    'link' => '/'.$item->section_alias.'/'.$item->category_alias.'/'.$item->item_alias,
                    'name' => $item->name,
                    'metroName' => $item->metroName,
                    'city' => $item->city,
                    'average' => $item->average,
                    'comments' => $item->comments,
                    'n' => $n,
                ];
                $result[] = $resultItem;
                $n++;
            }
        }
        return $result;
    }
}
