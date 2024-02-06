<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddImageGalleryRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id' => 'required|gt:0',
            'condition' => 'required',
            'description' => 'required|string|min:30|latin_characters|no_spam_links',
            'myfile' => 'required|file|mimes:jpeg,png,jpeg|max:5120|min:100'
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'item_id не передан',
            'item_id.gt' => 'item_id не передан',
            'condition.required' => 'Вы не согласились с условиями.',
            'description.latin_characters' => 'Описания на латинице запрещены',
            'description.no_spam_links' => 'Спам не пройдет!',
            'description.min' => 'Минимальное описание 30 символов',
            'description.required' => 'Заполните описание.',
            'myfile.required' => 'Выберите файл для загрузки',
            'myfile.file' => 'Файл не выбран',
            'myfile.mimes' => 'Поддерживаются только файлы с расширениями .jpg, .jpeg и .png.',
            'myfile.max' => 'Максимальный размер файла 5 МБ',
            'myfile.min' => 'Минимальный размер файла 100 КБ',
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
