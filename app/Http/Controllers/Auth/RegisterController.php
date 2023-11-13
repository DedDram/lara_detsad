<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
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
        $this->middleware('guest');
    }

    // Метод для показа формы регистрации
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Добавьте метод register, даже если он пустой
    public function register(Request $request)
    {
        // Создание нового пользователя
        if (empty($request->input('agent'))) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'status' => 1
            ]);
        } else {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'status' => 0,
                'users_group' => 2,
                'sad_id' => $request->input('item_id')
            ]);
        }

        // Отправка уведомления о подтверждении email
        $user->sendEmailVerificationNotification();

        // Вход после регистрации
        Auth::login($user);
        if (!empty($request->input('agent'))) {
            $data = ['status' => 1, 'msg' => 'Вам на почту ушло письмо, перейди по ссылке в нем для подтверждения email.'];
            return response()->json($data);
        } else {
            return redirect('/verification-message'); // Перенаправление после успешной регистрации
        }

    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

}
