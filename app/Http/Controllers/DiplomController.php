<?php

namespace App\Http\Controllers;

use App\Models\DetSad\Diplom;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiplomController
{
    private object $item;

    /**
     * @throws BindingResolutionException
     */
    public function __construct(Request $request, Diplom $diplom)
    {
        if($request->filled('id'))
        {
            $id = $request->query('id');
            $city = $request->query('city') ?? 0;
            $district = $request->query('district') ?? 0;
            $this->item = $diplom->getItem($id, $city, $district);
            if (!$this->item) {
                throw new BindingResolutionException('Item not found', 404);
            }
        }else{
            abort(404);
        }
    }
    public function code(): View
    {
        return view('diplom.code');
    }

    public function default(): View
    {
        return view('diplom.default', ['item' => $this->item]);
    }
}
