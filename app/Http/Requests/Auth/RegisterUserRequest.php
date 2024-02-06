<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterUserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|min:5',
            'password' => 'required|string',
            'email' => [
                'required',
                'string',
                'email',
                'unique:users',
                'regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Пожалуйста, введите имя',
            'name.min' => 'Минимальная длина имени 5 символов',
            'password.required' => 'Пожалуйста, введите пароль',
            'email.unique' => 'Пользователь с таким email уже зарегистрирован',
            'email.required' => 'Пожалуйста, введите E-mail',
            'email.email' => 'Пожалуйста, введите корректный E-mail',
            'email.regex' => 'Пожалуйста, введите корректный E-mail',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        //200 ответ не правильно, но лень переписывать js обработчик
        throw new HttpResponseException(
            response()->json([
                'status' => 2,
                'msg' => $validator->errors()->first(),
            ], 200)
        );
    }
}
