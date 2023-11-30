<?php

namespace App\Http\Controllers;

use App\Models\DetSad\Item;
use App\Models\Exchange_Job\ExchangeJobItems;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function getResponse(Request $request)
    {
        //карта на вкладке Сады Рядом
        if($request->query('task') == 'map' && !empty($request->json()))
        {
            $geoLat = $request->json('geo_lat');
            $geoLong = $request->json('geo_long');
            return Item::AjaxMapGeoShow((float) $geoLat, (float) $geoLong);
        }
        //показ телефоне и email на вкладке /obmen-mest
        if($request->query('task') == 'mailAds' && $request->filled('item_id'))
        {
            return ExchangeJobItems::getEmail((int) $request->query('item_id'));
        }
        if($request->query('task') == 'phoneAds' && $request->filled('item_id'))
        {
            return ExchangeJobItems::getPhone((int) $request->query('item_id'));
        }
    }
}
