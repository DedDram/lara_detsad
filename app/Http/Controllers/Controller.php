<?php

namespace App\Http\Controllers;

use App\Models\ContentCategory;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function main()
    {
        $title = 'Детские сады Москвы - отзывы, адреса, рейтинг';
        $metaKey = 'детские, сады, сад, отзывы, Москвы, детский';
        $metaDesc = 'Отзывы о детских сада 🧒, мнения, рейтинги 📈, адреса, телефоны ☎️, поиск на карте.🌍';
        return view('detsad.main',
            [
                'title' => $title,
                'metaKey' => $metaKey,
                'metaDesc' => $metaDesc,
            ]);
    }

    public function ClassesMain()
    {
        $title = 'Конспекты занятий для детского сада';
        $metaKey = 'занятия, детский, сад, конспекты, скачать, 2-3, лет, года';
        $metaDesc = 'Конспекты занятий для детского сада 🧒, занятия с детьми 2-3 лет 👶';
        return view('detsad.classes',
            [
                'title' => $title,
                'metaKey' => $metaKey,
                'metaDesc' => $metaDesc,
            ]);
    }

    public function ClassesCategory($category_id, $category_alias)
    {
        $category = ContentCategory::find($category_id);
        if(!empty($category) && $category->alias !== $category_alias)
        {
            return redirect()->to("/zanyatiya/$category->id-$category->alias");
        }
        $contentCategory = $category->contents;
        return view('detsad.categoryClasses',
            [
                'title' => $category->title,
                'metaKey' => $category->metakey,
                'metaDesc' => $category->metadesc,
                'items' => $contentCategory,
            ]);
    }
}
