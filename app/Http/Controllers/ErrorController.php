<?php

namespace App\Http\Controllers;

use App\Models\DetSad\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ErrorController
{
    public function getResponse(Request $request): View|\Illuminate\Http\JsonResponse
    {
        if ($request->isMethod('get')) {
            return view('detsad.error', ['id'=> $request->query('id'), 'item_id' => $request->query('item_id') ?? 0]);
        } else {
            $id = $request->input('id');
            $item_id = $request->input('item_id');
            $url = Item::getUrlSadik($id);

            if(empty($item_id)){
                $subject = 'Ошибка в материале id='.$id;
            }else{
                $subject = 'Жалоба на отзыв id='.$item_id;
            }

            $messageText = 'E-mail: '.$request->input('mailfrom').'<br>'
                .$request->input('description').'<br>Страница садика: <a href="https://detskysad.com'.$url->url.'">'.$url->name.'</a>';


            Mail::send([], [], function ($message) use ($subject, $messageText) {
                $message->to(config('mail.from.address'))
                    ->subject($subject)
                    ->html($messageText);
            });
            $data = ['msg' => 'Ваше сообщение успешно отправлено'];
            return response()->json($data);
        }
    }
}
