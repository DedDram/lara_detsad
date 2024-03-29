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
    protected Comments $comments;

    public function __construct(Comments $comments)
    {
        $this->comments = $comments;
    }

    public function getResponse(Request $request)
    {
        $task = $request->query('task', '');
        $session = '';

        if ($request->filled(['item_id', 'object_group', 'object_id'])) {
            $text = match ($task) {
                'unpublish' => 'Комментарий снят с публикации',
                'publish' => 'Комментарий опубликован',
                'remove' => 'Комментарий удален',
                'blacklist' => 'Пользователь заблокирован',
                default => ''
            };

            if ($text !== '') {
                $session = $task;
                $this->handleTask($task, $request->get('item_id'));
            }
        } elseif ($task === 'unsubscribe' && $request->filled(['object_group', 'object_id'])) {
            $this->comments->unsubscribe($request->get('object_group'), $request->get('object_id'), Auth::id());
            $session = 'unsubscribe';
            $text = 'Вы отписались от новых уведомлений';
        } else {
            return redirect('/login', 301);
        }

        $url = ($request->get('object_group') == 'com_content') ?
            Content::getUrl($request->get('object_id')) :
            Item::getUrlSadik($request->get('object_id'));

        return redirect(env('APP_URL') . $url->url, 301)->with($session, $text);
    }

    protected function handleTask(string $task, $itemId): void
    {
        match ($task) {
            'unpublish' => $this->comments->unpublishItems($itemId),
            'publish' => $this->comments->publishItems($itemId),
            'remove' => $this->comments->remove($itemId),
            'blacklist' => $this->comments->blacklist($itemId),
        };
    }
}
