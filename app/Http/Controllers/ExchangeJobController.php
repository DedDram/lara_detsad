<?php

namespace App\Http\Controllers;

use App\Models\Exchange_Job\ExchangeJobItems;
use App\Models\Exchange_Job\ExchangeJobTeachers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExchangeJobController
{
    public object $city;
    public object $metro;

    public string $cityName = '';
    public string $metroName = '';

    public function exchange(int $cityId = 0, string $cityAlias = '', int $metroId = 0, string $metroAlias = '')
    {
        $exchange = new ExchangeJobItems();

        // Redirect alias
        self::RedirectAlias($exchange, 'obmen-mest', $cityId, $cityAlias, $metroId, $metroAlias);

        $items = $exchange->getItems($cityId, $metroId);
        $city = $exchange->getCitySearch();
        $metro = $exchange->getMetroSearch();

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
                'cityName' => $this->cityName,
                'metroName' => $this->metroName,
                'metaDesc' => $metaDesc,
                'metaKey' => $metaKey,
            ]);
    }

    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            $exchange = new ExchangeJobItems();
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
                $exchangeItem = new ExchangeJobItems();
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


    public function job(int $cityId = 0, string $cityAlias = '', int $metroId = 0, string $metroAlias = '')
    {
        $exchange = new ExchangeJobItems();
        // Redirect alias
        self::RedirectAlias($exchange, 'rabota', $cityId, $cityAlias, $metroId, $metroAlias);

        $items = $exchange->getItems($cityId, $metroId);
        $city = $exchange->getCitySearch();
        $teachers = ExchangeJobTeachers::getTeachersSearch();
        $metro = $exchange->getMetroSearch();

        if(!empty($cityName)){
            if(!empty($metroName)){
                $metaKey = 'детские, сады, работа, вакансии, '.$cityName. ', '.$metroName;
                $metaDesc = '❤️ Работа в детских садах ✎ вакансии ☎️ объявления ✅'.$cityName. ' метро '.$metroName;
                $title = 'Работа в детских садах '.$cityName. ' метро '.$metroName;
            }else{
                $metaDesc = '❤️ Работа в детских садах ✎ вакансии ☎️ объявления ✅'.$cityName;
                $metaKey = 'детские, сады, обмен, мест, '.$cityName;
                $title = 'Работа в детских садах '.$cityName;
            }
        }else{
            $title = 'Работа в детских садах';
            $metaKey = 'детские, сады, работа, объявления';
            $metaDesc = '❤️ Работа в детских садах ✎ вакансии ☎️ объявления ✅';
        }
        return view('detsad.job',
            [
                'items' => $items,
                'city' => $city,
                'metro' => $metro,
                'title' => $title,
                'teachers' => $teachers,
                'cityName' => $this->cityName,
                'metroName' => $this->metroName,
                'metaDesc' => $metaDesc,
                'metaKey' => $metaKey,
            ]);
    }

    private function RedirectAlias(object $exchange, string $firstAlias, int $cityId = 0, string $cityAlias = '', int $metroId = 0, string $metroAlias = '')
    {
        // Redirect alias
        if(!empty($cityId) || !empty($metroId))
        {
            if(!empty($cityId))
            {
                $this->city = $exchange->getCity($cityId)
                    ->where('id', $cityId)
                    ->first();

                if (empty($this->city->alias)) {
                    abort(404);
                }
                $this->cityName = $this->city->name;
            }
            if(!empty($metroId))
            {
                $this->metro = $exchange->getMetro($metroId)
                    ->where('id', $cityId)
                    ->first();

                if (empty($this->metro->alias)) {
                    abort(404);
                }
                $this->metroName = $this->metro->name;
            }
            if(!empty($this->city) &&  $cityId. '-' .$cityAlias != $this->city->id.'-'.$this->city->alias){
                redirect()->to("/$firstAlias/" . $this->city->id.'-'.$this->city->alias)->send();
                exit();
            }
            if(!empty($this->metro) && ($metroId. '-' .$metroAlias != $this->metro->id.'-'.$this->metro->alias || $cityId. '-' .$cityAlias != $this->city->id.'-'.$this->city->alias)){
                redirect()->to("/$firstAlias/" . $this->city->id.'-'.$this->city->alias . '/' . $this->metro->id.'-'.$this->metro->alias)->send();
                exit();
            }
        }
    }
}
