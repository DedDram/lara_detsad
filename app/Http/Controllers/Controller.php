<?php

namespace App\Http\Controllers;

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
}
