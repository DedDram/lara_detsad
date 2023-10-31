<?php

namespace App\Http\Controllers;

use App\Models\Comments\Comments;
use App\Models\Content;
use App\Models\DetSad\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCommentsController extends Controller
{
    public function getResponse(Request $request)
    {
        $task = $request->query('task', '');
        $session = $text = '';

        if (Auth::check() && User::isAdmin() &&  !empty($task) && $request->filled('item_id') && $request->filled('object_group') && $request->filled('object_id')) {
            if ($task === 'unpublish') {
                (new Comments)->unpublishItems($request->get('item_id'));
                $session = 'unpublish';
                $text = 'Комментарий снят с публикаци';
            }
            if ($task === 'publish') {
                (new Comments)->publishItems($request->get('item_id'));
                $session = 'publish';
                $text = 'Комментарий опубликован';
            }
            if ($task === 'remove') {
                (new Comments)->remove($request->get('item_id'));
                $session = 'remove';
                $text = 'Комментарий удален';
            }
            if ($task === 'blacklist') {
                (new Comments)->blacklist($request->get('item_id'));
                $session = 'blacklist';
                $text = 'Пользователь заблокирован';
            }

        } elseif (Auth::check() && $request->filled('task') && $task == 'unsubscribe' && $request->filled('object_group') && $request->filled('object_id')) {
            (new Comments)->unsubscribe($request->get('object_group'), $request->get('object_id'), Auth::id());
            $session = 'unsubscribe';
            $text = 'Вы отписались от новых уведомлений';
        } else {
            return redirect('/login', 301);
        }
        // Получаем ссылку на страницу, где отзыв и редирект туда
        if ($request->get('object_group') == 'com_content') {
            $url = Content::getUrl($request->get('object_id'));
        } else {
            $url = Item::getUrlSadik($request->get('object_id'));
        }

        return redirect( env('APP_URL'). $url->url, 301)->with($session, $text);
    }
}
