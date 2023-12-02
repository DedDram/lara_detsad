<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentCategory;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\View\View;

class Controller extends BaseController
{
    public function main(): View
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

    public function addSad(): View
    {
        $title = 'Добавить детский сад';
        return view('detsad.add',['title' => $title]);
    }

    public function contact(): View
    {
        $title = 'Обратная связь';
        return view('detsad.contact',['title' => $title]);
    }

    public function ClassesMain(): View
    {
        $title = 'Конспекты занятий для детского сада';
        $metaKey = 'занятия, детский, сад, конспекты, скачать, 2-3, лет, года';
        $metaDesc = 'Конспекты занятий для детского сада 🧒, занятия с детьми 2-3 лет 👶';
        return view('content.classes',
            [
                'title' => $title,
                'metaKey' => $metaKey,
                'metaDesc' => $metaDesc,
            ]);
    }

    public function ClassesCategory(int $category_id, string $category_alias): View|\Illuminate\Http\RedirectResponse
    {
        $category = ContentCategory::find($category_id);
        if(!empty($category) && $category->alias !== $category_alias)
        {
            return redirect()->to("/zanyatiya/$category->id-$category->alias");
        }
        $contentCategory = $category->contents;
        return view('content.categoryClasses',
            [
                'title' => $category->title,
                'category_id' => $category_id,
                'category_alias' => $category_alias,
                'metaKey' => trim($category->metakey) ?: $category->title,
                'metaDesc' => trim($category->metadesc) ?: $category->title,
                'items' => $contentCategory,
            ]);
    }

    public function ClassesContent(int $category_id, string $category_alias, int $id, string  $alias): View|\Illuminate\Http\RedirectResponse
    {
        $content = new Content();
        $article = $content->getContent($id);
        if(!empty($article) && ($article->alias !== $alias || $category_id.'-'.$category_alias !== $article->cat_id.'-'.$article->cat_alias))
        {
            return redirect()->to("$article->cat_id.'-'.$article->cat_alias/$article->id.'-'.$article->alias");
        }
        return view('content.classesContent',
            [
                'title' => $article->title,
                'metaKey' => $article->metakey,
                'metaDesc' => $article->metadesc,
                'article' => $article,
            ]);
    }
}
