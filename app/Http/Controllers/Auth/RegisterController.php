<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Mail\AgentNotification;
use App\Models\DetSad\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    // Метод для показа формы регистрации
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterUserRequest $request)
    {

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
        if ($request->has('agent')) {
            $data = ['status' => 1, 'msg' => 'Вам на почту ушло письмо, перейдите по ссылке в нем для подтверждения email.'];
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
