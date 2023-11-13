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
                    $dir = public_path('images' . DIRECTORY_SEPARATOR . 'detsad' . DIRECTORY_SEPARATOR . $item_id);
                    // Проверяем существование директории перед созданием
                    if (!file_exists($dir)) {
                        mkdir($dir, 0755, true);
                    }

                    // Генерируем уникальное имя файла и сохраняем файл
                    $uniqueName = md5(uniqid(rand(), 1));
                    $uploadedFile->move($dir, $uniqueName);

                    // Обрезаем и делаем превью
                    $this->resizeAndCreateThumbnail($dir, $uniqueName, $fileExtension);

                    // Пишем в базу
                    $image = new DetsadImage();
                    $image->item_id = $item_id;
                    $image->original_name = $uniqueName . '.' . $fileExtension;
                    $image->thumb = $uniqueName . '_thumb.' . $fileExtension;
                    $image->alt = $description;
                    $image->title = $description;
                    $image->verified = 0;
                    $image->save();

                    // Send email notification
                    $sadikUrl = Item::getUrlSadik($item_id);

                    $messageText = '<div style="overflow: hidden;"><img src="' . config('app.url') . '/images/detsad/' . $item_id . '/'.$uniqueName . '_thumb.' . $fileExtension.'" style="float: left; margin-right: 10px;"/><br>'
                        . '<strong>Садик:</strong> <a href="' . config('app.url') . $sadikUrl->url . '">' . $sadikUrl->name . '</a><br>'
                        . '<strong>Описание фото:</strong> ' . $description . '</div>'
                        . '<div style="margin-top: 7px;">'
                        . '<a style="margin-right: 5px;" href="' . config('app.url') . '/images/detsad/' . $item_id . '/'.$uniqueName . '.' . $fileExtension.'">Оригинал фото</a>'
                        . '<a style="margin-right: 5px;" href="' . config('app.url') .'/remove-image-gallery?id='. $item_id. '&original_name='.$uniqueName . '.' . $fileExtension.'">Удалить фото</a>'
                        . '<a style="margin-right: 5px;" href="' . config('app.url') .'/publish-image-gallery?id='. $item_id . '&original_name='.$uniqueName . '.' . $fileExtension.'">Опубликовать фото</a>'
                        . '</div>';
                    $subject = 'Новое фото добавлено для Садика';
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
                        'msg' => 'Выберите файл для загрузки'
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

    private function resizeAndCreateThumbnail($dir, $uniqueName, $extension): void
    {
        $filePath = $dir. DIRECTORY_SEPARATOR .$uniqueName;

        // Resize the image
        $image = Image::make($filePath);
        $image->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Добавьте расширение к имени файла
        $originalFilePath = $dir. DIRECTORY_SEPARATOR .$uniqueName.'.'.$extension;

        // Сохраните изображение с расширением
        $image->save($originalFilePath);

        // Create thumbnail
        $thumbFilePath = $dir. DIRECTORY_SEPARATOR .$uniqueName . '_thumb.' . $extension;

        $image->resize(200, 200, function ($constraint) {
            $constraint->aspectRatio();
        });

        // Сохраните миниатюру с расширением
        $image->save($thumbFilePath);
        // Удалите оригинальный файл
        unlink($filePath);
    }

    public function remove(Request $request): string
    {
        $original_name = $request->query('original_name');
        $sad_id = $request->query('id');

        $image = DetsadImage::where('item_id', $sad_id)
            ->where('original_name', $original_name)
            ->first();

        if (!empty($image)) {
            if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->sad_id == $sad_id)) {

                $filePath = public_path("images/detsad/{$sad_id}/{$image->original_name}");
                $filePath2 = public_path("images/detsad/{$sad_id}/{$image->thumb}");

                if (file_exists($filePath)) {
                    DetsadImage::where('item_id', $sad_id)
                        ->where('original_name', $original_name)
                        ->delete();

                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }

                    if (file_exists($filePath2)) {
                        unlink($filePath2);
                    }

                    return 'Фото удалено!';
                } else {
                    return 'Ошибка удаления';
                }
            }else{
                return 'Фото может удалить только админ или представитель этого садика';
            }

        }
        return 'Фото не найдено!';
    }

    public function publish(Request $request): string
    {
        $original_name = $request->query('original_name');
        $sad_id = $request->query('id');

        $image = DetsadImage::where('item_id', $sad_id)
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
