<?php

namespace App\Http\Controllers;

use App\Models\Exchange_Job\ExchangeJobItems;
use App\Models\Exchange_Job\ExchangeJobTeachers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;

class ExchangeJobController
{
    public object $city;
    public object $metro;

    public string $cityName = '';
    public string $metroName = '';

    public function exchange(Request $request, int $cityId = 0, string $cityAlias = '', int $metroId = 0, string $metroAlias = '')
    {
        $exchange = new ExchangeJobItems();

        // Redirect alias
        self::RedirectAlias($exchange, 'obmen-mest', $cityId, $cityAlias, $metroId, $metroAlias);

        $items = $exchange->getItems($request, 'exchange', $cityId, $metroId);
        $city = $exchange->getCitySearch('exchange');
        $metro = $exchange->getMetroSearch('exchange');

        if(!empty($this->cityName)){
            if(!empty($this->metroName)){
                $metaKey = 'детские, сады, обмен, мест, '.$this->cityName. ', '.$this->metroName;
                $metaDesc = ' ❤️ Обмен мест в детских садах ✎ телефоны ☎️ объявления ✅'.$this->cityName. ' метро '.$this->metroName;
                $title = 'Обмен местами в детских садах '.$this->cityName. ' метро '.$this->metroName;
            }else{
                $metaDesc = ' ❤️ Обмен мест в детских садах ✎ телефоны ☎️ объявления ✅'.$this->cityName;
                $metaKey = 'детские, сады, обмен, мест, '.$this->cityName;
                $title = 'Обмен местами в детских садах '.$this->cityName;
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
            $city = $exchange->getCitySearch('exchange');
            $metro = $exchange->getMetroSearch('exchange');

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


    public function job(Request $request, int $cityId = 0, string $cityAlias = '', int $metroId = 0, string $metroAlias = '')
    {
        $exchange = new ExchangeJobItems();
        // Redirect alias
        self::RedirectAlias($exchange, 'rabota', $cityId, $cityAlias, $metroId, $metroAlias);

        $items = $exchange->getItems($request, 'job', $cityId, $metroId);
        $city = $exchange->getCitySearch('job');
        $teachers = ExchangeJobTeachers::getTeachersSearch();
        $metro = $exchange->getMetroSearch('job');

        if(!empty($this->cityName)){
            if(!empty($this->metroName)){
                $metaKey = 'детские, сады, работа, вакансии, '.$this->cityName. ', '.$this->metroName;
                $metaDesc = '❤️ Работа в детских садах ✎ вакансии ☎️ объявления ✅'.$this->cityName. ' метро '.$this->metroName;
                $title = 'Работа в детских садах '.$this->cityName. ' метро '.$this->metroName;
            }else{
                $metaDesc = '❤️ Работа в детских садах ✎ вакансии ☎️ объявления ✅'.$this->cityName;
                $metaKey = 'детские, сады, обмен, мест, '.$this->cityName;
                $title = 'Работа в детских садах '.$this->cityName;
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

    public function addJob(Request $request)
    {
        if ($request->isMethod('get')) {
            $exchange = new ExchangeJobItems();
            $city = $exchange->getCitySearch('exchange');
            $metro = $exchange->getMetroSearch('exchange');
            $teachers = ExchangeJobTeachers::getTeachersSearch();

            return view('detsad.addJob',
                [
                    'city' => $city,
                    'metro' => $metro,
                    'teachers' => $teachers
                ]);
        } else {
            //валидация данных формы

            $validatorData = $this->validateUserData($request);
            $teachers = $request->input('teacher', []);

            if ($validatorData['status'] === 2) {
                return response()->json($validatorData);
            } else {
                $exchangeItem = new ExchangeJobItems();
                // если есть фото
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    if ($file->isValid()) {
                        // Генерация уникального имени файла
                        $originalFileName = md5(uniqid(rand(), 1)) . '.' . $file->getClientOriginalExtension();

                        // Проверка разрешенных форматов
                        $allowedExtensions = ['jpg', 'jpeg', 'png'];
                        if (!in_array(strtolower($file->getClientOriginalExtension()), $allowedExtensions)) {
                            return [
                                'status' => 2,
                                'msg' => 'Недопустимый формат файла. Разрешены только JPG и PNG.',
                            ];
                        }

                        // Перемещение файла
                        $file->move(public_path('/images/rabota'), $originalFileName);

                        // Создаем и обрабатываем миниатюру
                        $image = Image::make(public_path('/images/rabota/' . $originalFileName));

                        // Проверяем ширину изображения и обрезаем, если она больше 80 пикселей
                        if ($image->width() > 80) {
                            $image->resize(80, 80, function ($constraint) {
                                $constraint->aspectRatio();
                            });

                            // Пересохраняем оригинальное изображение
                            $image->save(public_path('/images/rabota/' . $originalFileName));
                        }
                        //сохраняем в базу
                        $exchangeItem->photo = $originalFileName;
                    } else {
                        return [
                            'status' => 2,
                            'msg' => 'Ошибка при загрузке файла.',
                        ];
                    }
                }
                if(!empty($teachers))
                {
                    $rows = ExchangeJobTeachers::whereIn('id', $teachers)->get();
                    $teach = [];
                    if(!empty($rows))
                    {
                        foreach($rows as $row)
                        {
                            $teach[] = $row->name;
                        }
                        $exchangeItem->teach = implode(', ', $teach);
                    }
                }else{
                    return array(
                        'status' => 2,
                        'msg' => 'Укажите специальность'
                    );
                }
                $city_id = preg_replace('~-(.*)~', '', $request->input('city_id'));
                $metro_id = preg_replace('~-(.*)~', '', $request->input('metro_id'));

                $exchangeItem->status = 0;
                $exchangeItem->ip = $request->ip();
                $exchangeItem->city_id = $city_id;
                $exchangeItem->metro_id = $metro_id;
                $exchangeItem->created = now();
                $exchangeItem->modified = now();
                $exchangeItem->fullname = $request->input('username');
                $exchangeItem->phone = $request->input('phone');
                $exchangeItem->email = $request->input('email');
                $exchangeItem->text = $request->input('text');
                $exchangeItem->type = $request->input('type');
                $exchangeItem->save();
                $data = array(
                    'status' => 1,
                    'msg' => 'Объявление будет опубликовано после проверки модератором'
                );
                $lastInsertedId = $exchangeItem->id;

                if(!empty($exchangeItem->photo))
                {
                    $src = 'https://detskysad.com/images/rabota/'.$exchangeItem->photo;
                }else{
                    $src = 'https://detskysad.com/images/stories/user.jpg';
                }
                $title = 'Резюме/вакансии - новое объявление';
                $messageText = '<img style="float: left; margin-right: 10px;" src="'.$src.'">
                <p>Имя: '.$request->input('username').'</p>
                <p>E-mail: '.$request->input('email').'</p>
                <p>Телефон: '.$request->input('phone').'</p>
                <p>Специальность: '.implode(', ', $teach).'</p>
                <p>Текст: '.$request->input('text').'</p>
                <a href="'.env('APP_URL').'/rabota/publish/'.$lastInsertedId.'">опубликовать</a>
                <a href="'.env('APP_URL').'/rabota/delete/'.$lastInsertedId.'">удалить</a>';

                Mail::send([], [], function ($message) use ($messageText, $title) {
                    $message->to(config('mail.from.address'))
                        ->subject($title)
                        ->setBody($messageText, 'text/html');
                });
            }
            return response()->json($data);
        }

    }

    public function publishJob(int $id): \Illuminate\Http\RedirectResponse
    {
        $result = ExchangeJobItems::find($id);
        if($result !== null)
        {
            $result->status = 1;
            $result->save();
            return redirect()->to('/rabota')->with('publish', 'Объявление опубликовано');
        }
        return redirect()->to('/rabota')->with('publish', 'Объявление не найдено!');
    }

    public function deleteJob(int $id): \Illuminate\Http\RedirectResponse
    {
        $result = ExchangeJobItems::find($id);
        if($result !== null)
        {
            if (!empty($result->photo) && file_exists(public_path('/images/rabota/' . $result->photo))) {
                unlink(public_path('/images/rabota/' . $result->photo));
            }
            $result->delete();
            return redirect()->to('/rabota')->with('remove', 'Объявление удалено');
        }
        return redirect()->to('/rabota')->with('remove', 'Объявление не найдено!');
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
            'text.required' => 'Пожалуйста, введите текст объявления',
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
                    ->where('id', $metroId)
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
