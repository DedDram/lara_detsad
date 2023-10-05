<?php

namespace App\Http\Controllers;

use App\Models\DetSad\Category;
use App\Models\DetSad\Section;
use App\Models\DetSad\Streets;
use stdClass;

class DetSadController
{
    public function section($sectionId, $sectionAlias)
    {
        $section =  Section::query()->find($sectionId);
        if (!empty($section)) {
            if ($sectionAlias != $section->alias) {
                return redirect()->to('/' . $sectionId . '-' . $section->alias);
            }
        } else {
            abort('404');
        }
        $categories = Category::query()->where('section_id', $sectionId)->get();
        $address = Section::getAddress($sectionId, $sectionAlias);
        return view('detsad.section',
            ['section' => $section,
                'categories' => $categories,
                'address' => $address,
                'title' => $section->title,
                'metaDesc' => $section->title . ' ❤️ отзывы о детских садах 😊 адреса на карте 🌎',
                'metaKey' => $section->title . ' отзывы, детские, сады',
            ]);
    }

    public function category($sectionId, $sectionAlias, $categoryId, $categoryAlias, $district = '')
    {
        $category = Category::getCategory($categoryId);
        if (!empty($category)) {
            if ($sectionId.'-'.$sectionAlias != $category->section_alias || $categoryId.'-'.$categoryAlias != $category->category_alias) {
                return redirect()->to('/' . $category->section_alias . '/' . $category->category_alias);
            }
        } else {
            abort('404');
        }
        $itemsCollection = Category::getItems($categoryId);
        $districts = Category::getDistricts($categoryId);
        if(!empty($district)){
            $district = Category::getDistrict($categoryId, $district);
        }else{
            $district = '';
        }
        $address = array();
        if(!empty($itemsCollection))
        {
            $n = 1;
            foreach ($itemsCollection as $key => $value) {
                $itemsCollection[$key]->link = '/'.$category->section_alias.'/'.$category->category_alias.'/'.$value->id.'-'.$value->alias;
                $itemsCollection[$key]->name = $value->name;
                $itemsCollection[$key]->okrug = $value->okrug;
                $itemsCollection[$key]->n = $n;
                $n++;
                $address[] = array(
                    'geo_lat' => $value->geo_lat,
                    'geo_long' => $value->geo_long,
                    'url' => '/'.$category->section_alias.'/'.$category->category_alias.'/'.$value->id.'-'.$value->alias,
                    'text' => $value->name
                );
            }
        }
        if(!empty($district)){
            $distr2 = trim(strrchr($district->name, ' '));
            $distr1 = trim(str_replace(array($distr2,'территориальный', 'административный'), '', $district->name));
            if($district->morfer_name){
                $title = 'Детские сады '.$district->morfer_name.'а '.$category->name;
                $metaDesc = 'Детские сады '.$district->morfer_name.'а '.$category->name . ' ❤️ отзывы о детских садах 😊 адреса на карте 🌎';
                $metaKey =  'Детские сады '.$district->morfer_name.'а '.$category->name . ' отзывы, детские, сады';
            }else{
                $title = 'Детские сады '.$distr2.'а '.$distr1.' г.'.$category->name;
                $metaDesc = 'Детские сады '.$distr2.'а '.$distr1.' г.'.$category->name . ' ❤️ отзывы о детских садах 😊 адреса на карте 🌎';
                $metaKey =  'Детские сады '.$distr2.'а '.$distr1.' г.'.$category->name . ' отзывы, детские, сады';
            }
        }else {
            $title = $category->title.' отзывы по районам на карте';
            $metaDesc = $category->title . ' ❤️ отзывы о детских садах 😊 адреса на карте 🌎';
            $metaKey = $category->title . ' отзывы, детские, сады';
        }

        return view('detsad.category',
            [
                'category' => $category,
                'address' => $address,
                'items' => $itemsCollection,
                'districts' => $districts,
                'district' => $district,
                'title' => $title,
                'baseUrl' => '/'.$category->section_alias.'/'.$category->category_alias,
                'metaDesc' => $metaDesc,
                'metaKey' => $metaKey,
            ]);
    }

    public function streets($categoryId, $categoryAlias)
    {
        self::checkRedirectCategory($categoryId, $categoryAlias);
        $streets = Streets::getStreets($categoryId, $categoryAlias);
        $title = 'Поиск по улице детского сада г.'.$streets['city'];
        $metaDesc = 'Поиск по улице детского сада '.$streets['city'].' ❤️ отзывы о детских садах ✎ телефоны ☎️ адреса, рейтинг ✅';
        $metaKey =  'Поиск, улица, детский, сад, '.$streets['city'];
        return view('detsad.streets',
            [
                'streets' => $streets,
                'title' => $title,
                'metaDesc' => $metaDesc,
                'metaKey' => $metaKey,
            ]);
    }

    public function street($categoryId, $categoryAlias, $street_alias)
    {
        $category = self::checkRedirectCategory($categoryId, $categoryAlias);
        $street = Streets::getStreet($categoryId, $street_alias);
        $items = Streets::getItems($categoryId, $street_alias);
        $address = array();
        if(!empty($items))
        {
            $n = 1;
            foreach ($items as $key => $value) {
                $items[$key]->link = '/'.$category->section_alias.'/'.$category->category_alias.'/'.$value->id.'-'.$value->alias;
                $items[$key]->name = $value->name;
                $items[$key]->okrug = $value->okrug;
                $items[$key]->n = $n;
                $n++;
                $address[] = array(
                    'geo_lat' => $value->geo_lat,
                    'geo_long' => $value->geo_long,
                    'url' => '/'.$category->section_alias.'/'.$category->category_alias.'/'.$value->id.'-'.$value->alias,
                    'text' => $value->name
                );
            }
        }
        $title = 'Детские сады — '.$street->name.' ('.$street->city.')';
        $metaDesc = 'Детские сады — '.$street->name.' ('.$street->city.') ❤️ отзывы о детских садах ✎ телефоны ☎️ адреса, рейтинг ✅';
        $metaKey =  'Поиск, улица, детский, сад, '.$street->name.' ('.$street->city.')';
        return view('detsad.street',
            [
                'street' => $street,
                'address' => $address,
                'items' => $items,
                'title' => $title,
                'metaDesc' => $metaDesc,
                'metaKey' => $metaKey,
            ]);
    }

    private function checkRedirectCategory ($categoryId, $categoryAlias)
    {
        $category = Category::getCategory($categoryId);
        if (!empty($category)) {
            if ($categoryAlias != $category->alias) {
                redirect()->to('/street/' . $categoryId . '-' . $category->alias);
            }
        } else {
            abort('404');
        }
        return $category;
    }
}
