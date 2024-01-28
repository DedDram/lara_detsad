<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DetSad\Item;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth'); // Убедитесь, что только аутентифицированные пользователи могут видеть этот контроллер
    }

    /**
     * @throws AuthenticationException
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->user()->markEmailAsVerified();
        event(new Verified($request->user()));

        if ($request->user()->hasVerifiedEmail()) {

            if($request->user()->users_group == 2){
                //уведомление админу о новом представителе садика

                $data = [
                    'user' => $request->user(),
                    'urlSadik' => Item::getUrlSadik($request->user()->sad_id),
                    'siteName' => config('app.url')
                ];
                $agent = 'Теперь дождитесь, когда администратор активирует ваш профиль. Как правило это занимает 5-30 минут.';
                Mail::send('mail.newAgent', $data, function ($message){
                    $message->to(config('mail.from.address'))
                        ->subject('Новый представитель садика');
                });
                //разлогиниваем агента, чтобы не шарился по личному кабинету пока не позволено.
                Auth::logout();
                return redirect()->route('verification.verified')->with('agent', $agent);
            }else{
                return redirect()->route('verification.notice');
            }
        }else{
            throw new AuthenticationException('Email is not verified.');
        }
    }


    public function show(Request $request)
    {
        return view('users.verificationSuccess');
    }
}
