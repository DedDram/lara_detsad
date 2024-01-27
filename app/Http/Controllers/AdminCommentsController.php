<?php

namespace App\Http\Controllers;

use App\Models\Comments\Comments;
use App\Models\Content;
use App\Models\DetSad\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCommentsController
{
    protected Comments $commentsService;

    public function __construct(Comments $commentsService)
    {
        $this->commentsService = $commentsService;
    }

    public function getResponse(Request $request)
    {
        if (!Auth::check() || !User::isAdmin()) {
            return redirect('/login', 301);
        }

        $task = $request->query('task', '');
        $session = $text = '';

        if ($request->filled(['item_id', 'object_group', 'object_id'])) {
            switch ($task) {
                case 'unpublish':
                    $this->commentsService->unpublishItems($request->get('item_id'));
                    $session = 'unpublish';
                    $text = 'Комментарий снят с публикации';
                    break;
                case 'publish':
                    $this->commentsService->publishItems($request->get('item_id'));
                    $session = 'publish';
                    $text = 'Комментарий опубликован';
                    break;
                case 'remove':
                    $this->commentsService->remove($request->get('item_id'));
                    $session = 'remove';
                    $text = 'Комментарий удален';
                    break;
                case 'blacklist':
                    $this->commentsService->blacklist($request->get('item_id'));
                    $session = 'blacklist';
                    $text = 'Пользователь заблокирован';
                    break;
            }
        } elseif ($task === 'unsubscribe' && $request->filled(['object_group', 'object_id'])) {
            $this->commentsService->unsubscribe($request->get('object_group'), $request->get('object_id'), Auth::id());
            $session = 'unsubscribe';
            $text = 'Вы отписались от новых уведомлений';
        } else {
            return redirect('/login', 301);
        }

        $url = ($request->get('object_group') == 'com_content') ? Content::getUrl($request->get('object_id')) : Item::getUrlSadik($request->get('object_id'));

        return redirect(env('APP_URL') . $url->url, 301)->with($session, $text);
    }
}
