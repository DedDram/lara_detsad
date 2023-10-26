<?php

namespace App\Models\Cron;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CronComments  extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        //обновление геолокации отзывов
        $items = DB::table('lwa7r_comments_items')
            ->select('*')
            ->whereNull('country')
            ->where('ip', '!=', '0')
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get();

        if(!empty($items))
        {
            foreach($items as $item)
            {
                $geo = self::YandexLocation(long2ip($item->ip));

                if(empty($geo->country))
                {
                    $geo->country = 'unknown';
                }
                if($geo->country == 'Россия' && !empty($geo->city))
                {
                    $geo->country = $geo->city;
                }
                DB::table('lwa7r_comments_items')
                    ->where('id', $item->id)
                    ->update(['country' => $geo->country]);
            }
        }
    }

    /**
     * Отправка писем уведомлений
     */
    public function getResponse(): void
    {
        $items = DB::table('lwa7r_comments_cron as t1')
            ->select('t1.id AS ids', 't1.type', 't1.title', 't1.url', 't2.id', 't2.images', 't2.status',
                't2.object_group', 't2.object_id', 't2.ip', 't2.user_id', 't2.country', 't2.created', 't2.description',
                DB::raw('IF(t2.user_id > 0, t3.name, t2.username) AS username'),
                DB::raw('IF(t2.user_id > 0, t3.email, t2.email) AS useremail'), 't4.email')
            ->join('lwa7r_comments_items as t2', 't1.item_id', '=', 't2.id')
            ->leftJoin('users as t3', 't2.user_id', '=', 't3.id')
            ->leftJoin('users as t4', 't1.user_id', '=', 't4.id')
            ->orderBy('t1.type', 'ASC')
            ->limit(5)
            ->get();

        $ids = array();

        if(!empty($items))
        {
            foreach($items as $item)
            {
                if($item->type == 1)
                {
                    $temp = self::user($item);

                    if(!empty($temp))
                    {
                        $ids[] = $item->ids;
                    }
                }

                if($item->type == 2 || $item->type == 3)
                {
                    $temp = self::admin($item);

                    if(!empty($temp))
                    {
                        $ids[] = $item->ids;
                    }
                }
            }
        }

        if(!empty($ids))
        {
            DB::table('lwa7r_comments_cron')
                ->whereIn('id', $ids)
                ->delete();
        }
    }


    /**
     * Отправка письму уведомления юзеру подписанному на отзывы
     */
    private function user($item): bool
    {
        $data = [
            'item' => $item,
            'siteName' => config('app.url'),
        ];

        Mail::send('mail.userNotification', $data, function ($message) use ($item) {
            $message->to($item->email)
                ->subject('Новый отзыв: ' . $item->title);
        });

        return true;
    }


    /**
     * * Отправка письму уведомления админу
     */
    private function admin($item): bool
    {
        if($item->type == 2)
        {
            $title = 'Добавлен новый отзыв';
        }else{
            $title = 'Редактирование отзыва';
        }
        $item->country = (!empty($item->country)) ? $item->country : 'Страна не определена';

        if(!empty($item->images))
        {
            $images = DB::table('lwa7r_comments_images')
                ->select('*')
                ->where('item_id', $item->id)
                ->get();
        }else{
            $images = null;
        }

        $data = [
            'item' => $item,
            'images' => $images,
            'siteName' => config('app.url')
        ];
        Mail::send('mail.adminNotification', $data, function ($message) use ($item, $title){
            $message->to(config('mail.from.address'))
                ->subject($title.': '.$item->title);
        });

        return true;
    }

    private static function YandexLocation($ip): object
    {
        // $yandex_api_keyLocator = 'ABvrmkwBAAAAMG8HdwIAOuIhmdroVhAsutIPfPXaWNwDDqMAAAAAAAAAAACYaQU04MKqqq7kiXYPr1nN2z0P8w==';
        $yandex_api_key = '7b8c5fa1-500f-48a5-875c-b253bb7b347c';
        $yandex_api_keyLocator = 'b952c57b-98c2-439d-bd6d-71aebc47849f';
        $data = (object) array(
            'common' => (object) array(
                'version' => '1.0',
                'api_key' => $yandex_api_keyLocator
            ),
            'ip' => (object) array(
                'address_v4' => $ip
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.lbs.yandex.net/geolocation");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, 'identity');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'json='.json_encode($data));
        $response = curl_exec($ch);
        curl_close($ch);

        $geo = json_decode($response, true);
        if(!empty($geo['position'])){
            $content = file_get_contents('https://geocode-maps.yandex.ru/1.x/?apikey='.$yandex_api_key.'&format=json&geocode='.$geo['position']['longitude'].','.$geo['position']['latitude']);
            $data = json_decode($content, true);

            if(!empty($data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['LocalityName']))
            {
                $city = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['LocalityName'];
            }else{
                $city = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['AdministrativeAreaName'];
            }
            return (object) array('country' => $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['CountryName'] ?? '', 'city' => $city ?? '');
        }else{
            return (object) array();
        }
    }

}
