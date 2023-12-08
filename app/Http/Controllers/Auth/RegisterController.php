<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    protected $rules = [
        'name' => 'required|min:5',
        'email' => 'required|string|email|unique:users',
        'password' => 'required|string',
    ];

    protected $messages = [
        'name.required' => 'Пожалуйста, введите имя',
        'name.min' => 'Минимальная длина имени 5 символов',
        'password.required' => 'Пожалуйста, введите пароль',
        'email.unique' => 'Пользователь с таким email уже зарегистрирован',
        'email.required' => 'Пожалуйста, введите E-mail',
        'email.email' => 'Пожалуйста, введите корректный E-mail',
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
        //валидация данных формы
        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        if ($validator->passes()) {
            // Создание нового пользователя
            if (!$request->has('agent')) {
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
            if (!$request->has('agent')) {
                $data = ['status' => 1, 'msg' => 'Вам на почту ушло письмо, перейди по ссылке в нем для подтверждения email.'];
                return response()->json($data);
            } else {
                return redirect('/verification-message'); // Перенаправление после успешной регистрации
            }
        }else{
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
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
