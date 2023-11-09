<?php

namespace App\Http\Controllers;

use App\Models\DetSad\Diplom;
use Illuminate\Http\Request;
class DiplomController
{
    private object $item;

    public function __construct(Request $request)
    {
        if((!empty($request->has('city')) || !empty($request->has('district'))) && !empty($request->has('id')))
        {
            $diplom = new Diplom();
            $id = $request->query('id');
            $city = $request->query('city') ?? 0;
            $district = $request->query('district') ?? 0;
            $this->item = $diplom->getItem($id, $city, $district);
        }
    }
    public function code()
    {
        return view('diplom.code');
    }

    public function default()
    {
        return view('diplom.default', ['item' => $this->item]);
    }
}
