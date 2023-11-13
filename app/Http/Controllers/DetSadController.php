<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelper;
use App\Models\Comments\Comments;
use App\Models\DetSad\AdsCity;
use App\Models\DetSad\Category;
use App\Models\DetSad\DetsadGallery;
use App\Models\DetSad\Item;
use App\Models\DetSad\Section;
use App\Models\DetSad\Streets;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class DetSadController
{
    public function getResponse(Request $request)
    {
        $task = $request->input('task');
        if(!empty($task) && ($task == 'telephone' || $task == 'mail'))
        {
            return Item::showTelephoneOrEmail($request);
        }
        if(!empty($task) && ($task == 'photo')){
            if ($request->isMethod('get')) {
                return view('detsad.addPhoto', ['item_id'=> $request->query('item_id')]);
            } else {
                $id = $request->input('item_id');
                $file = $request->file('file');

                $messageText = 'Добавлено фото к садику id=' . $id;
                $subject = 'Добавлено фото к садику id=' . $id;

                Mail::send([], [], function ($message) use ($subject, $messageText, $file) {
                    $message->to(config('mail.from.address'))
                        ->subject($subject)
                        ->setBody($messageText, 'text/html');

                    if ($file) {
                        // Прикрепляем файл к письму
                        $message->attach($file->getRealPath(), [
                            'as' => $file->getClientOriginalName(),
                            'mime' => $file->getMimeType(),
                        ]);
                    }
                });

                $data = ['msg' => 'Ваше сообщение успешно отправлено'];
                return response()->json($data);
            }
        }

    }
    public function section(int $sectionId, string $sectionAlias)
    {
        $section = Section::query()->find($sectionId);
        if ($section !== null) {
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

    public function category(int $sectionId, string $sectionAlias, int $categoryId, string $categoryAlias, string $district = '')
    {
        $category = Category::getCategory($categoryId);
        if ($category !== null) {
            if ($sectionId . '-' . $sectionAlias != $category->section_alias || $categoryId . '-' . $categoryAlias != $category->category_alias) {
                return redirect()->to('/' . $category->section_alias . '/' . $category->category_alias);
            }
        } else {
            abort('404');
        }
        $itemsCollection = Category::getItems($categoryId, $district);
        $districts = Category::getDistricts($categoryId);
        if (!empty($district)) {
            $district = Category::getDistrict($categoryId, $district);
            if ($district == null) {
                abort(404);
            }
        } else {
            $district = '';
        }
        $address = array();
        if ($itemsCollection->isNotEmpty()) {
            $n = 1;
            foreach ($itemsCollection as $key => $value) {
                $itemsCollection[$key]->link = '/' . $category->section_alias . '/' . $category->category_alias . '/' . $value->id . '-' . $value->alias;
                $itemsCollection[$key]->name = $value->name;
                $itemsCollection[$key]->okrug = $value->okrug;
                $itemsCollection[$key]->n = $n;
                $n++;
                $address[] = array(
                    'geo_lat' => $value->geo_lat,
                    'geo_long' => $value->geo_long,
                    'url' => '/' . $category->section_alias . '/' . $category->category_alias . '/' . $value->id . '-' . $value->alias,
                    'text' => $value->name
                );
            }
        }
        $itemsCollection = $itemsCollection->sortByDesc('average');
        if (!empty($district)) {
            $distr2 = trim(strrchr($district->name, ' '));
            $distr1 = trim(str_replace(array($distr2, 'территориальный', 'административный'), '', $district->name));
            if ($district->morfer_name) {
                $title = 'Детские сады ' . $district->morfer_name . 'а ' . $category->name;
                $metaDesc = 'Детские сады ' . $district->morfer_name . 'а ' . $category->name . ' ❤️ отзывы о детских садах 😊 адреса на карте 🌎';
                $metaKey = 'Детские сады ' . $district->morfer_name . 'а ' . $category->name . ' отзывы, детские, сады';
            } else {
                $title = 'Детские сады ' . $distr2 . 'а ' . $distr1 . ' г.' . $category->name;
                $metaDesc = 'Детские сады ' . $distr2 . 'а ' . $distr1 . ' г.' . $category->name . ' ❤️ отзывы о детских садах 😊 адреса на карте 🌎';
                $metaKey = 'Детские сады ' . $distr2 . 'а ' . $distr1 . ' г.' . $category->name . ' отзывы, детские, сады';
            }
        } else {
            $title = $category->title . ' отзывы по районам на карте';
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
                'baseUrl' => '/' . $category->section_alias . '/' . $category->category_alias,
                'metaDesc' => $metaDesc,
                'metaKey' => $metaKey,
            ]);
    }

    public function streets(int $categoryId, string $categoryAlias)
    {
        self::checkRedirectCategory($categoryId, $categoryAlias);
        $streets = Streets::getStreets($categoryId, $categoryAlias);
        $title = 'Поиск по улице детского сада г.' . $streets['city'];
        $metaDesc = 'Поиск по улице детского сада ' . $streets['city'] . ' ❤️ отзывы о детских садах ✎ телефоны ☎️ адреса, рейтинг ✅';
        $metaKey = 'Поиск, улица, детский, сад, ' . $streets['city'];
        return view('detsad.streets',
            [
                'streets' => $streets,
                'title' => $title,
                'metaDesc' => $metaDesc,
                'metaKey' => $metaKey,
            ]);
    }

    public function street(int $categoryId, string $categoryAlias, string $street_alias)
    {
        $category = self::checkRedirectCategory($categoryId, $categoryAlias);
        $street = Streets::getStreet($categoryId, $street_alias);
        $items = Streets::getItems($categoryId, $street_alias);
        $address = array();
        if ($items->isNotEmpty()) {
            $n = 1;
            foreach ($items as $key => $value) {
                $items[$key]->link = '/' . $category->section_alias . '/' . $category->category_alias . '/' . $value->id . '-' . $value->alias;
                $items[$key]->name = $value->name;
                $items[$key]->okrug = $value->okrug;
                $items[$key]->n = $n;
                $n++;
                $address[] = array(
                    'geo_lat' => $value->geo_lat,
                    'geo_long' => $value->geo_long,
                    'url' => '/' . $category->section_alias . '/' . $category->category_alias . '/' . $value->id . '-' . $value->alias,
                    'text' => $value->name
                );
            }
        }
        $title = 'Детские сады - ' . $street->name . ' (' . $street->city . ')';
        $metaDesc = 'Детские сады - ' . $street->name . ' (' . $street->city . ') ❤️ отзывы о детских садах ✎ телефоны ☎️ адреса, рейтинг ✅';
        $metaKey = 'Поиск, улица, детский, сад, ' . $street->name . ' (' . $street->city . ')';
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

    public function sadik(int $sectionId, string $sectionAlias, int $categoryId, string $categoryAlias, int $sadId, string $sadAlias)
    {
        $item = new Item();
        $sadik = $item->getItem($sadId);

        //проверка верности алиасов
        self::redirectWrongAlias($sadik, $sectionId, $sectionAlias, $categoryId, $categoryAlias, $sadId, $sadAlias);
        $sadik = self::adsCity($sadik, $sectionId);

        $url = '/' . $sadik->section_alias . '/' .  $sadik->category_alias . '/' . $sadik->item_alias;
        $addresses = Item::getAddress($sectionId, $sectionAlias, $categoryId, $categoryAlias, $sadik->section_name, $sadik->category_name, $sadId);
        $statistics = Item::getStatistics($sadId);
        $fields = Item::getFields($sadId);
        $commentsTitle = StringHelper::declension($sadik->comments, ['отзыв', 'отзыва', 'отзывов']);

        if (count($addresses) == 1 && $addresses[0]->locality == 'Москва') {
            $street = ' ' . $addresses[0]->street_address;
        } else {
            $street = '';
        }
        $items = Comments::getItems(request(), 'com_detsad', $sadik->id, 10);
        $user = Auth::user();
        //(показ только плохих или только хороших комментов + JS
        $procentGood = $procentNeutrally = $procentBad = $modulePosition = $good = $neutrally = $bad = 0;
        if($items->isNotEmpty()){
            $modulePosition = floor($items->total() / 2);
            $procentGood = ($items[0]->good * 100) / ($items[0]->good + $items[0]->neutrally + $items[0]->bad);
            $procentNeutrally = ($items[0]->neutrally * 100) / ($items[0]->good + $items[0]->neutrally + $items[0]->bad);
            $procentBad = ($items[0]->bad * 100) / ($items[0]->good + $items[0]->neutrally + $items[0]->bad);
            $good = $items[0]->good;
            $neutrally = $items[0]->neutrally;
            $bad = $items[0]->bad;
        }
        $request = FacadesRequest::instance();
        $num = $request->input('num');
        $ratingCount = explode(" ", $commentsTitle);
        if(!empty($sadik->preview_src)){
            $imagePath = '/images/detsad/' . $sadik->id . '/' . $sadik->preview_src;
            $fullPath = public_path($imagePath);

            if (file_exists($fullPath)) {
                $image = Image::make($fullPath);
                $widthImage = $image->width();
                $heightImage = $image->height();
            } else{
                $widthImage = 220;
                $heightImage = 165;
            }
        }else{
            $widthImage = 220;
            $heightImage = 165;
        }

        $title = $sadik->name.$street.' - '.$commentsTitle;
        $metaDesc = $sadik->title.' ❤️ описание садика ✎ телефоны ☎️ адреса, ОТЗЫВЫ, рейтинг ✅';
        $metaKey = $sadik->title.', телефон, адрес, отзывы';

        return view('detsad.sadik',
            [
                'url' => $url,
                'item' => $sadik,
                'user' => $user,
                'widthImage' => $widthImage,
                'heightImage' => $heightImage,
                'countImage' => $sadik->count_img,
                'addresses' => $addresses,
                'fields' => $fields,
                'statistics' => $statistics,
                'ratingCount' => $ratingCount,
                'title' => $title,
                'metaDesc' => $metaDesc,
                'metaKey' => $metaKey,
                'object_group' => 'com_detsad',
                'object_id' => $sadik->id,
                'items' => $items,
                'countComments' => $items->total(),
                'num' => $num,
                'good' => $good,
                'neutrally' => $neutrally,
                'bad' => $bad,
                'procentGood' => $procentGood,
                'procentNeutrally' => $procentNeutrally,
                'procentBad' => $procentBad,
                'modulePosition' => $modulePosition,
            ]);
    }

    public function gallery(int $sectionId, string $sectionAlias, int $categoryId, string $categoryAlias, int $sadId, string $sadAlias)
    {
        $item = new Item();
        $sadik = $item->getItem($sadId);
        //проверка верности алиасов
        self::redirectWrongAlias($sadik, $sectionId, $sectionAlias, $categoryId, $categoryAlias, $sadId, $sadAlias);
        $sadik = self::adsCity($sadik, $sectionId);
        $gallery = Item::getGallery($sadId);
        $url = '/' . $sadik->section_alias . '/' .  $sadik->category_alias . '/' . $sadik->item_alias;
        return view('detsad.gallery',
            [
                'url'=>$url,
                'gallery' => $gallery,
                'object_group' => 'com_detsad',
                'title' => 'Фото ' . $sadik->title,
                'item' => $sadik
            ]);

    }


    public function addGallery(Request $request)
    {
        $total = Item::getCountImage($request->query('id'));
        return view('detsad.addGallery',['item_id'=>$request->query('id'), 'total' => $total]);
    }

    public function addGalleryPost(Request $request): \Illuminate\Http\JsonResponse
    {
        $gallery = new DetsadGallery();
        $addImg = $gallery->add($request);
        return response()->json($addImg);
    }

    public function delImageGallery(Request $request): \Illuminate\Http\RedirectResponse
    {
        $gallery = new DetsadGallery();
        $with = $gallery->remove($request);
        return redirect()->back()->with('removeImgError', $with);
    }
    public function PublishImageGallery(Request $request): \Illuminate\Http\RedirectResponse
    {
        $gallery = new DetsadGallery();
        $with = $gallery->publish($request);
        $linkSadik = Item::getUrlSadik($request->query('id'));
        return redirect()->to(config('app.url').$linkSadik->url.'/gallery')->with('publishImgOk', $with);
    }

    public function sadAgent(int $sectionId, string $sectionAlias, int $categoryId, string $categoryAlias, int $sadId, string $sadAlias)
    {
        $item = new Item();
        $sadik = $item->getItem($sadId);
        //проверка верности алиасов
        self::redirectWrongAlias($sadik, $sectionId, $sectionAlias, $categoryId, $categoryAlias, $sadId, $sadAlias);
        $sadik = self::adsCity($sadik, $sectionId);
        $url = '/' . $sadik->section_alias . '/' .  $sadik->category_alias . '/' . $sadik->item_alias;
        return view('detsad.agent',['url'=>$url, 'item' => $sadik,'title' => 'Представитель '.$sadik->name]);

    }

    public function registrationAgentGet(Request $request)
    {
        return view('detsad.registrationAgent',['request' => $request]);
    }

    private function adsCity(object $sadik, int $sectionId): object
    {
        if ($sectionId > 1 && $sectionId < 15) {
            $ads_city = 'moskva';
        } else {
            $ads_city = $sadik->ads_city;
        }
        $sadik->ads_url = '';
        $ads_city_ = '';
        if (strpos($ads_city, 'j') !== false) {
            $ads_city_ = str_replace('j', 'y', $ads_city);
        }
        $city_ = AdsCity::getCity($ads_city, $ads_city_);
        if ($city_ !== null) {
            $sadik->ads_url = '/obmen-mest/' . $city_->id . '-' . $city_->alias;
        }
        return $sadik;
    }

    private function redirectWrongAlias(object $sadik,int $sectionId, string $sectionAlias, int $categoryId, string $categoryAlias, int $sadId, string $sadAlias): void
    {
        if (!empty($sadik)) {
            if ($sectionId . '-' . $sectionAlias != $sadik->section_alias ||
                $categoryId . '-' . $categoryAlias != $sadik->category_alias ||
                $sadAlias != $sadik->item_alias
            ) {
                redirect()->to('/' . $sadik->section_alias . '/' . $sadik->category_alias . '/' . $sadik->item_alias);
            }
        } else {
            abort('404');
        }
    }
    private function checkRedirectCategory($categoryId, $categoryAlias)
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
