<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ErrorController extends Controller
{
    public function getResponse(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('detsad.error', ['id'=> $request->query('id')]);
        } else {
            $id = $request->input('id');
            $messageText = $request->input('description');
            $subject = 'Ошибка в материале id='.$id;
            Mail::send([], [], function ($message) use ($subject, $messageText) {
                $message->to(config('mail.from.address'))
                    ->subject($subject)
                    ->setBody($messageText, 'text/html');
            });
            $data = ['msg' => 'Ваше сообщение успешно отправлено'];
            return response()->json($data);
        }
    }
}
