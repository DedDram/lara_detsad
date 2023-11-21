<?php

namespace App\Http\Controllers;




use App\Models\Exchange\ExchangeItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExchangeDetSadController
{
    public function exchange(int $cityId = 0, string $cityAlias = '', int $metroId = 0, string $metroAlias = '')
    {
        $exchange = new ExchangeItems($attributes = [], $cityId, $cityAlias, $metroId, $metroAlias);

        $items = $exchange->getItems($cityId, $metroId);
        $city = $exchange->getCitySearch();
        $metro = $exchange->getMetroSearch();
        $cityName = $exchange->getCityName();
        $metroName = $exchange->getMetroName();

        if(!empty($cityName)){
            if(!empty($metroName)){
                $metaKey = 'детские, сады, обмен, мест, '.$cityName. ', '.$metroName;
                $metaDesc = ' ❤️ Обмен мест в детских садах ✎ телефоны ☎️ объявления ✅'.$cityName. ' метро '.$metroName;
                $title = 'Обмен местами в детских садах '.$cityName. ' метро '.$metroName;
            }else{
                $metaDesc = ' ❤️ Обмен мест в детских садах ✎ телефоны ☎️ объявления ✅'.$cityName;
                $metaKey = 'детские, сады, обмен, мест, '.$cityName;
                $title = 'Обмен местами в детских садах '.$cityName;
            }
        }else{
            $title = 'Обмен местами в детских садах';
            $metaKey = 'детские, сады, обмен, мест';
            $metaDesc = ' ❤️ Обмен мест в детских садах ✎ телефоны ☎️ объявления ✅';
        }
        return view('detsad.exchange',
            [
                'items' => $items,
                'city' => $city,
                'metro' => $metro,
                'title' => $title,
                'cityName' => $cityName,
                'metroName' => $metroName,
                'metaDesc' => $metaDesc,
                'metaKey' => $metaKey,
            ]);
    }

    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            $exchange = new ExchangeItems();
            $city = $exchange->getCitySearch();
            $metro = $exchange->getMetroSearch();

            return view('detsad.addExchange',
                [
                    'city' => $city,
                    'metro' => $metro,
                ]);
        } else {
            //валидация данных формы
            $validatorData = $this->validateUserData($request);
            if ($validatorData['status'] === 2) {
                return response()->json($validatorData);
            } else {
                $city_id = preg_replace('~-(.*)~', '', $request->input('city_id'));
                $metro_id = preg_replace('~-(.*)~', '', $request->input('metro_id'));
                $exchangeItem = new ExchangeItems();
                $exchangeItem->status = 1;
                $exchangeItem->ip = $request->ip();
                $exchangeItem->city_id = $city_id;
                $exchangeItem->metro_id = $metro_id;
                $exchangeItem->created = now();
                $exchangeItem->modified = now();
                $exchangeItem->fullname = $request->input('username');
                $exchangeItem->phone = $request->input('phone');
                $exchangeItem->email = $request->input('email');
                $exchangeItem->text = $request->input('text');
                $exchangeItem->save();
                $data = array(
                    'status' => 1,
                    'msg' => 'Объявление будет опубликовано после проверки модератором'
                );
            }
            return response()->json($data);
        }

    }

    private function validateUserData(Request $request): array
    {
        $rules = [
            'text' => 'required|string|min:50|latin_characters|no_spam_links',
            'username' => 'required',
            'email' => 'required|email',
            'phone' => 'required|string',
        ];

        $messages = [
            'text.required' => 'Пожалуйста, введите текст отзыва',
            'text.min' => 'Минимальная длина объявления 50 символов',
            'text.latin_characters' => 'Объявления на латинице запрещены',
            'text.no_spam_links' => 'Спам не пройдет!',
            'username.required' => 'Пожалуйста, введите Ваше имя',
            'phone.required' => 'Пожалуйста, введите Телефон',
            'email.required' => 'Пожалуйста, введите E-mail',
            'email.email' => 'Пожалуйста, введите корректный E-mail',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return [
                'status' => 2,
                'msg' => $validator->errors()->first(),
            ];
        } else {
            return [
                'status' => 1
            ];
        }
    }
}
