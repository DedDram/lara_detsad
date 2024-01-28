<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CommentCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $rules = [];
        if(!Auth::check() || (Auth::check() && !User::isAdmin())){
            $rules = [
                'description' => 'required|string|min:100|latin_characters|no_spam_links',
                'object_group' => 'required|string',
                'object_id' => 'required|gt:0',
            ];

            if (!Auth::check()) {
                $rules['username'] = 'required';
                $rules['email'] = 'required|email';
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'description.required' => 'Пожалуйста, введите текст отзыва',
            'description.min' => 'Минимальная длина отзыва - 100 символов',
            'description.latin_characters' => 'Отзывы на латинице запрещены',
            'description.no_spam_links' => 'Спам не пройдет!',
            'username.required' => 'Пожалуйста, введите Ваше имя',
            'email.required' => 'Пожалуйста, введите E-mail',
            'email.email' => 'Пожалуйста, введите корректный E-mail',
            'object_group.required' => 'Не передан object_group',
            'object_id.required' => 'Не передан object_id',
            'object_id.gt' => 'Не передан object_id',
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
