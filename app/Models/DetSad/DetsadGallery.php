<?php

namespace App\Models\DetSad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;

class DetsadGallery extends Model
{

    public function add($request): array
    {
        $item_id = $request->input('item_id');
        $description = $request->input('description');
        $uploadedFile = $request->file('myfile');

        // расширение файла
        $fileExtension = $uploadedFile->getClientOriginalExtension();

        // Создаем папку, если её нет
        $dir = $this->createDirectory($item_id);

        // Генерируем уникальное имя файла и сохраняем файл
        $uniqueFileName = md5(uniqid(rand(), 1));
        $uploadedFile->move($dir, $uniqueFileName);

        // Обрезаем и делаем превью
        $this->resizeAndCreateThumbnail($dir, $uniqueFileName, $fileExtension);

        try {
            // Пишем в базу
            $image = new DetsadImage();
            $image->item_id = $item_id;
            $image->original_name = $uniqueFileName . '.' . $fileExtension;
            $image->thumb = $uniqueFileName . '_thumb.' . $fileExtension;
            $image->alt = $description;
            $image->title = $description;
            $image->verified = 0;
            $image->save();

            // Отправляем уведомление на почту
            $this->sendEmailNotification($item_id, $uniqueFileName, $fileExtension, $description);

            return [
                'status' => 1,
                'msg' => 'Изображение загружено и будет добавлено после проверки модератором.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 2,
                'msg' => 'Ошибка сохранения в базу данных: ' . $e->getMessage()
            ];
        }
    }


    public function remove(Request $request): string
    {
        if ($request->filled('original_name') && $request->filled('id')) {
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
                        $image->delete();

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
                } else {
                    return 'Фото может удалить только админ или представитель этого садика';
                }

            }
        }

        return 'Фото не найдено!';
    }

    public function publish(Request $request): string
    {
        if ($request->filled('original_name') && $request->filled('id')) {
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
        }

        return 'Фото не найдено!';
    }

    private function resizeAndCreateThumbnail($dir, $uniqueName, $extension): void
    {
        $filePath = $dir . DIRECTORY_SEPARATOR . $uniqueName;
        $image = Image::make($filePath);
        $image->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $originalFilePath = $dir . DIRECTORY_SEPARATOR . $uniqueName . '.' . $extension;
        $image->save($originalFilePath);
        $thumbFilePath = $dir . DIRECTORY_SEPARATOR . $uniqueName . '_thumb.' . $extension;
        $image->resize(200, 200, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->save($thumbFilePath);
        unlink($filePath);
    }

    private function createDirectory(int $item_id): string
    {
        $dir = public_path('images' . DIRECTORY_SEPARATOR . 'detsad' . DIRECTORY_SEPARATOR . $item_id);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    private function sendEmailNotification(int $item_id, string $uniqueFileName, string $fileExtension, string $description): void
    {
        $sadikUrl = Item::getUrlSadik($item_id);

        $messageText = '<div style="overflow: hidden;"><img src="' . config('app.url') . '/images/detsad/' . $item_id . '/' . $uniqueFileName . '_thumb.' . $fileExtension . '" style="float: left; margin-right: 10px;"/><br>'
            . '<strong>Садик:</strong> <a href="' . config('app.url') . $sadikUrl->url . '">' . $sadikUrl->name . '</a><br>'
            . '<strong>Описание фото:</strong> ' . $description . '</div>'
            . '<div style="margin-top: 7px;">'
            . '<a style="margin-right: 5px;" href="' . config('app.url') . '/images/detsad/' . $item_id . '/' . $uniqueFileName . '.' . $fileExtension . '">Оригинал фото</a>'
            . '<a style="margin-right: 5px;" href="' . config('app.url') . '/remove-image-gallery?id=' . $item_id . '&original_name=' . $uniqueFileName . '.' . $fileExtension . '">Удалить фото</a>'
            . '<a style="margin-right: 5px;" href="' . config('app.url') . '/publish-image-gallery?id=' . $item_id . '&original_name=' . $uniqueFileName . '.' . $fileExtension . '">Опубликовать фото</a>'
            . '</div>';
        $subject = 'Новое фото добавлено для Садика';

        Mail::send([], [], function ($message) use ($subject, $messageText) {
            $message->to(config('mail.from.address'))
                ->subject($subject)
                ->html($messageText);
        });
    }

}
