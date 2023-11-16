<?php

namespace App\Http\Controllers;

use App\Models\DetSad\Item;
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

    }
}
