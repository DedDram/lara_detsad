<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AfterCreatCommentGeoLocation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //обновление геолокации отзывов
        $items = DB::table('i1il4_comments_items')
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
                $geo = self::YandexLocation($item->ip);

                if(empty($geo->country))
                {
                    $geo->country = 'unknown';
                }
                if($geo->country == 'Россия' && !empty($geo->city))
                {
                    $geo->country = $geo->city;
                }
                DB::table('i1il4_comments_items')
                    ->where('id', $item->id)
                    ->update(['country' => $geo->country]);
            }
        }


    }



    private static function YandexLocation($ip): object
    {

        $yandex_api_key = '067bbf35-de27-4de2-bb1c-72d958556cad';
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'json=' . json_encode($data));
        $response = curl_exec($ch);
        curl_close($ch);

        $geo = json_decode($response, true);

        try {
            if (!empty($geo['position'])) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://geocode-maps.yandex.ru/1.x/?apikey=' . $yandex_api_key . '&format=json&geocode=' . $geo['position']['longitude'] . ',' . $geo['position']['latitude']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_REFERER, 'https://detskysad.com');
                $content = curl_exec($ch);
                curl_close($ch);
            } else {
                return (object)array();
            }
        } catch (Exception $e) {
            return (object)array();
        }

        if (!empty($content)) {
            $data = json_decode($content, true);

            if (!empty($data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['LocalityName'])) {
                $city = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['LocalityName'];
            } else {
                $city = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['AdministrativeAreaName'];
            }
            return (object)array('country' => $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['CountryName'], 'city' => $city);
        } else {
            return (object)array();
        }
    }
}
