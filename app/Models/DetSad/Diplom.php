<?php

namespace App\Models\DetSad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Diplom extends Model
{
    public function getItem(int $id, int $city, int $district)
    {
        $item = Item::query()
            ->from('i1il4_detsad_items AS items')
            ->when(!empty($district), function ($query) {
                $query->leftJoin('i1il4_detsad_categories AS categories', 'categories.id', '=', 'items.category_id');
            }, function ($query) {
                $query->leftJoin('i1il4_detsad_address AS address', 'address.item_id', '=', 'items.id');
            })
            ->where('items.id', $id)
            ->select('items.*')
            ->addSelect(DB::raw(!empty($district) ? 'categories.name AS district' : 'address.locality AS locality'))
            ->first();

        $tpl = '/images/diploms/rus.jpg';
        $country = 'России';
        $where = "section_id NOT IN (17,20,21) ORDER BY average DESC";

        if ($item) {
            if ($item->section_id == 17) {
                $tpl = '/images/diploms/ukr.jpg';
                $country = 'Украины';
                $where = "section_id = 17 ORDER BY average DESC";
            } elseif ($item->section_id == 21) {
                $tpl = '/images/diploms/bel.jpg';
                $country = 'Беларуси';
                $where = "section_id = 21 ORDER BY average DESC";
            } elseif ($item->section_id == 20) {
                $tpl = '/images/diploms/kaz.jpg';
                $country = 'Казахстана';
                $where = "section_id = 20 ORDER BY average DESC";
            }
        }

        if (!empty($city) && !empty($item->locality)) {
            $xreg = 'г.';
            $country = $xreg . $item->locality;
            $rows = Item::query()
                ->from('i1il4_detsad_items AS items')
                ->select('items.*')
                ->leftJoin('i1il4_detsad_address AS address', 'items.id', '=', 'address.item_id')
                ->where('address.locality', $item->locality)
                ->orderBy('items.average', 'DESC')
                ->get();

            if ($rows->isNotEmpty()) {
                $item->n = $this->getRate($rows, $id);
            }
        } elseif (!empty($district) && !empty($item->district)) {
            $xreg = 'района ';
            $country = $xreg . $item->district;
            $rows = Item::select('id', 'name', 'average')
                ->where('category_id', $item->category_id)
                ->orderBy('average', 'DESC')
                ->get();
            if ($rows->isNotEmpty()) {
                $item->n = $this->getRate($rows, $id);
            }
        } else {
            $rows = Item::select('id', 'name', 'average')
                ->whereRaw($where)
                ->get();
            if ($rows->isNotEmpty()) {
                $item->n = $this->getRate($rows, $id);
            }
        }

        $item->country = $country;
        $item->tpl = $tpl;

        return $item;
    }

    private function getRate(object $rows, int $id): int
    {
        $rate = 0;
        $joined = array();
        foreach($rows as $key=>$value){
            $value->average = (string) $value->average;
            if(!array_key_exists($value->average, $joined)){
                $plus = array($value->average => '');
                $joined = $joined + $plus;
            }
            $joined[$value->average] .= '|'.$value->id;
        }
        $i = 1;
        foreach($joined as $element){
            if(str_contains($element . '|', '|' . $id . '|')){
                $rate = $i;
                break;
            }
            $i++;
        }
        return $rate;
    }
}
