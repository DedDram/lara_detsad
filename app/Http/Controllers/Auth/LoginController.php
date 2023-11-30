<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/profile';
    protected $rules = [
        'email' => 'required|string|email',
        'password' => 'required|string',
    ];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function logout()
    {
        Auth::logout(); // Разлогиниваем пользователя
        return redirect('/'); // Перенаправляем пользователя, куда угодно после разлогинивания
    }


    // Метод для показа формы входа
    public function showLoginForm()
    {
        return view('auth.login', ['title' => 'Авторизация']);
    }

    // Метод для обработки входа
    public function login(Request $request): \Illuminate\Http\RedirectResponse
    {

        // Попытка аутентификации пользователя
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->first();
            if ($user && $user->email_verified_at !== null && !empty($user->status)) {
                // Если email подтвержден, перенаправляем пользователя
                return redirect()->intended('/profile'); // Перенаправление после успешного входа
            } else {
                // Если email не подтвержден, выход и перенаправление с сообщением
                Auth::logout();
                if(empty($user->status)){
                    return redirect()->back()->withInput($request->only('email'))->withErrors([
                        'email' => 'Дождитесь активации аккаунта администратором.',
                    ]);
                }else{
                    return redirect()->back()->withInput($request->only('email'))->withErrors([
                        'email' => 'Ваш аккаунт еще не подтвержден (нужно перейти по ссылке из письма, которое высылают после регистрации)',
                    ]);
                }
            }
        }

        // Перенаправление в случае неудачного входа
        return redirect()->back()->withInput($request->only('email'))->withErrors([
            'email' => 'Такого пользователя нет или Ваш аккаунт еще не подтвержден (нужно перейти по ссылке из письма, которое высылается после регистрации)',
        ]);
    }
}
