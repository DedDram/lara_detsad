<?php

namespace App\Models\DetSad;

use Illuminate\Support\Facades\DB;

class Streets
{
    public static function getStreets(int $categoryId, string $sectionAlias): ?array
    {
        if ($categoryId == 1) {
            $results = DB::table('i1il4_detsad_address AS t1')
                ->select('t1.street_address AS name', 't1.locality', 't1.street_alias AS alias')
                ->leftJoin('i1il4_detsad_items AS t2', 't1.item_id', '=', 't2.id')
                ->leftJoin('i1il4_detsad_categories AS t3', 't2.category_id', '=', 't3.id')
                ->where('t1.locality', 'Москва')
                ->where('t1.street_alias', '!=', '')
                ->orderBy('name')
                ->get();
        } else {
            $results = DB::table('i1il4_detsad_address AS t1')
                ->select('t1.street_address AS name', 't1.locality', 't1.street_alias AS alias')
                ->leftJoin('i1il4_detsad_items AS t2', 't1.item_id', '=', 't2.id')
                ->leftJoin('i1il4_detsad_categories AS t3', function ($join) {
                    $join->on('t2.category_id', '=', 't3.id')
                        ->on('t1.locality', '=', 't3.name');
                })
                ->where('t3.id', $categoryId)
                ->where('t1.street_alias', '!=', '')
                ->orderBy('name')
                ->get();
        }
        if($results->isNotEmpty()){
            $streets = array(
                'city_id' => $categoryId,
                'city_alias' => $sectionAlias,
                'city' => $results[0]->locality,
                'streets' => array()
            );
            foreach ($results as $result) {
                $result->name = trim(substr($result->name, 0, strpos($result->name, ',')));
                unset($result->locality);
                $streets['streets'][$result->alias][] = $result;
            }
            return $streets;
        }

        return null;
    }

    public static function getStreet(int $categoryId, string $streetAlias)
    {
        if ($categoryId == 1) {
            $street = DB::table('i1il4_detsad_address AS t1')
                ->select('t1.street_address AS name', 't1.locality AS city', 't1.street_alias AS alias')
                ->leftJoin('i1il4_detsad_items AS t2', 't1.item_id', '=', 't2.id')
                ->leftJoin('i1il4_detsad_categories AS t3', 't2.category_id', '=', 't3.id')
                ->where('t1.locality', 'Москва')
                ->where('t1.street_alias', $streetAlias)
                ->first();
        } else {
            $street = DB::table('i1il4_detsad_address AS t1')
                ->select('t1.street_address AS name', 't1.locality AS city', 't1.street_alias AS alias')
                ->leftJoin('i1il4_detsad_items AS t2', 't1.item_id', '=', 't2.id')
                ->leftJoin('i1il4_detsad_categories AS t3', function ($join) {
                    $join->on('t2.category_id', '=', 't3.id')
                        ->on('t1.locality', '=', 't3.name');
                })
                ->where('t3.id', $categoryId)
                ->where('t1.street_alias',  $streetAlias)
                ->first();
        }
        $street->name = trim(substr($street->name, 0, strpos($street->name, ',')));

        return $street;
    }

    public static function getItems(int $categoryId, string $streetAlias): \Illuminate\Support\Collection
    {
        if ($categoryId == 1) {
            return DB::table('i1il4_detsad_items as t1')
                ->select(DB::raw('DISTINCT t1.id'),'t1.*', 't4.geo_lat', 't4.geo_long',DB::raw('CONCAT_WS("-", t1.id, t1.alias) AS item_alias'),
                    DB::raw('CONCAT_WS("-", t2.id, t2.alias) AS category_alias'),
                    DB::raw('CONCAT_WS("-", t3.id, t3.alias) AS section_alias'))
                ->leftJoin('i1il4_detsad_categories as t2', 't1.category_id', '=', 't2.id')
                ->leftJoin('i1il4_detsad_sections as t3', 't1.section_id', '=', 't3.id')
                ->leftJoin('i1il4_detsad_address as t4', 't1.id', '=', 't4.item_id')
                ->where('t4.locality', '=', 'Москва')
                ->where('t4.street_alias', $streetAlias)
                ->get();
        } else {
            return DB::table('i1il4_detsad_items as t1')
                ->select(DB::raw('DISTINCT t1.id'),'t1.*', 't4.geo_lat', 't4.geo_long',DB::raw('CONCAT_WS("-", t1.id, t1.alias) AS item_alias'),
                    DB::raw('CONCAT_WS("-", t2.id, t2.alias) AS category_alias'),
                    DB::raw('CONCAT_WS("-", t3.id, t3.alias) AS section_alias'))
                ->leftJoin('i1il4_detsad_categories as t2', 't1.category_id', '=', 't2.id')
                ->leftJoin('i1il4_detsad_sections as t3', 't1.section_id', '=', 't3.id')
                ->leftJoin('i1il4_detsad_address as t4', function ($join) {
                    $join->on('t1.id', '=', 't4.item_id')
                    ->on('t4.locality', '=', 't2.name');
                })
                ->where('t2.id', $categoryId)
                ->where('t4.street_alias', $streetAlias)
                ->get();
        }
    }
}
