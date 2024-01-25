<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

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
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
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
        ];
    }
}
