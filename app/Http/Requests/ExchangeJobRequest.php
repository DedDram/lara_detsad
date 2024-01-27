<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;


class ExchangeJobRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        if(request()->has('type')){
            return [
                'type' => 'required|not_in:0',
                'city_id' => 'required|not_in:0',
                'text' => 'required|string|min:50|latin_characters|no_spam_links',
                'username' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'teacher' => 'required',
            ];
        }else{
            return [
                'city_id' => 'required|not_in:0',
                'text' => 'required|string|min:50|latin_characters|no_spam_links',
                'username' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
            ];
        }
    }

    public function messages(): array
    {
        return [
            'teacher.required' => 'Пожалуйста, выберите профессию',
            'type.not_in' => 'Пожалуйста, выберите Резюме или Вакансия',
            'text.required' => 'Пожалуйста, введите текст объявления',
            'text.min' => 'Минимальная длина объявления 50 символов',
            'text.latin_characters' => 'Объявления на латинице запрещены',
            'text.no_spam_links' => 'Спам не пройдет!',
            'username.required' => 'Пожалуйста, введите Ваше имя',
            'phone.required' => 'Пожалуйста, введите Телефон',
            'email.required' => 'Пожалуйста, введите E-mail',
            'email.email' => 'Пожалуйста, введите корректный E-mail',
            'city_id.required' => 'Пожалуйста, выберите город',
            'city_id.not_in' => 'Пожалуйста, выберите город',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'errors' => $validator->errors()->all(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
