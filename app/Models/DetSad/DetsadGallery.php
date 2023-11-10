<?php

namespace App\Models\DetSad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class DetsadGallery extends Model
{

    public function add(Request $request): array
    {
        $item_id = $request->input('item_id', 0);
        $condition = $request->input('condition', 0);
        $description = $request->input('description', '');

        if (!empty($condition)) {
            if (!empty($description)) {
                $uploadedFile = $request->file('myfile');

                if ($uploadedFile) {
                    // Проверяем расширение файла
                    $allowedExtensions = ['jpg', 'jpeg', 'png'];
                    $fileExtension = $uploadedFile->getClientOriginalExtension();

                    if (!in_array($fileExtension, $allowedExtensions)) {
                        // Если расширение файла не допустимо, возвращаем статус 2
                        return [
                            'status' => 2,
                            'msg' => 'Поддерживаются только файлы с расширениями .jpg, .jpeg и .png.'
                        ];
                    }
                    // Создаем папку, если её нет
                    $dir = 'public/images/detsad/' . $item_id;
                    Storage::makeDirectory($dir);

                    // Генерируем уникальное имя файла
                    $uniqueName = md5(uniqid(rand(), 1));
                    $originalFilePath = $uploadedFile->storeAs($dir, $uniqueName);

                    // Resize and create a thumbnail
                    $filePath = $this->resizeAndCreateThumbnail($dir, $uniqueName, $fileExtension);

                    // Insert image information into the database
                    $image = new DetsadImage();
                    $image->item_id = $item_id;
                    $image->original = $uniqueName . '.' . $fileExtension;
                    $image->thumb = $uniqueName . '_thumb.' . $fileExtension;
                    $image->alt = $description;
                    $image->title = $description;
                    $image->verified = 0;
                    $image->save();

                    // Send email notification
                    $sadikUrl = Item::getUrlSadik($item_id);

                    $messageText = '<div style="overflow: hidden;"><img src="' . config('app.url') . Storage::url($filePath[1]) . '" style="float: left; margin-right: 10px;"/><br>'
                        . '<strong>ВУЗ:</strong> <a href="' . config('app.url') . $sadikUrl->url . '">' . $sadikUrl->name . '</a><br>'
                        . '<strong>Описание фото:</strong> ' . $description . '</div>'
                        . '<div style="margin-top: 7px;">'
                        . '<a style="margin-right: 5px;" href="' . config('app.url') . Storage::url($filePath[0]) . '">Оригинал фото</a>'
                        . '<a style="margin-right: 5px;" href="' . config('app.url') .'/remove-image-gallery?id='. $image->item_id . '&original_name='.$uniqueName . '.' . $fileExtension.'">Удалить фото</a>'
                        . '<a style="margin-right: 5px;" href="' . config('app.url') .'/publish-image-gallery?id='. $image->item_id . '&original_name='.$uniqueName . '.' . $fileExtension.'">Опубликовать фото</a>'
                        . '</div>';
                    $subject = 'Новое фото добавлено для ВУЗа';
                    //уведомление админу о новом фото
                    Mail::send([], [], function ($message) use ($subject, $messageText) {
                        $message->to(config('mail.from.address'))
                            ->subject($subject)
                            ->setBody($messageText, 'text/html');
                    });

                    return [
                        'status' => 1,
                        'msg' => 'Изображение загружено и будет добавлено после проверки модератором.'
                    ];
                } else {
                    return [
                        'status' => 2,
                        'msg' => 'Укажите файл для загрузки'
                    ];
                }
            } else {
                return [
                    'status' => 2,
                    'msg' => 'Заполните описание.'
                ];
            }
        } else {
            return [
                'status' => 2,
                'msg' => 'Вы не согласились с условиями.'
            ];
        }
    }

    private function resizeAndCreateThumbnail($dir, $uniqueName, $extension): array
    {
        $filePath = storage_path("app/$dir/$uniqueName");

        // Resize the image
        $image = Image::make($filePath);
        $image->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Добавьте расширение к имени файла
        $originalFilePath = "$dir/$uniqueName.$extension";

        // Сохраните изображение с расширением
        $image->save(storage_path("app/$originalFilePath"));

        // Create thumbnail
        $thumbFilePath = "$dir/$uniqueName" . '_thumb.' . $extension;

        $image->resize(200, 200, function ($constraint) {
            $constraint->aspectRatio();
        });

        // Сохраните миниатюру с расширением
        $image->save(storage_path("app/$thumbFilePath"));
        // Удалите оригинальный файл
        unlink($filePath);

        return [$originalFilePath, $thumbFilePath];
    }

    public function remove(Request $request)
    {
        $original_name = $request->query('original_name');
        $vuz_id = $request->query('id');

        $image = VuzImage::where('item_id', $vuz_id)
            ->where('original_name', $original_name)
            ->first();

        if (!empty($image)) {
            if (Auth::user()->isAdmin() || Auth::user()->vuz_id == $vuz_id) {
                $filePath = "public/images/vuz/{$vuz_id}/{$image->original_name}";
                $filePath2 = "public/images/vuz/{$vuz_id}/{$image->thumb}";
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                    Storage::delete($filePath2);

                    VuzImage::where('item_id', $vuz_id)
                        ->where('original_name', $original_name)
                        ->delete();

                    return 'Фото удалено!';
                } else {
                    return 'Ошибка удаления';
                }
            }else{
                return 'Фото может удалить только админ или представитель этого ВУЗа';
            }

        }
        return 'Фото не найдено!';
    }

    public function publish(Request $request)
    {
        $original_name = $request->query('original_name');
        $vuz_id = $request->query('id');

        $image = VuzImage::where('item_id', $vuz_id)
            ->where('original_name', $original_name)
            ->first();

        if (!empty($image)) {
            $image->verified = 1;
            $image->save();
            return 'Фото опубликовано!';
        }
        return 'Фото не найдено!';
    }

}
