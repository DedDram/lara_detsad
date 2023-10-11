<?php

namespace App\Models\Comments;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Comments extends Model
{
    protected $table = 'i1il4_comments_items';
    protected $primaryKey = 'id';
    protected $fillable = ['object_group', 'object_id', 'created', 'ip', 'user_id', 'rate', 'country', 'status', 'username', 'email', 'isgood', 'ispoor', 'description', 'images'];

    public static function getCommentUser(int $user_id)
    {
        return self::where('user_id', $user_id)
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function totalCommentsCount()
    {
        return self::count();
    }

    public static function getItems($request, $objectGroup, $objectId, $limit)
    {
        if (Auth::check() && User::isAdmin()) {
            $items = self::select('i1il4_comments_items.*', 'i1il4_comments_items.username AS guest_name', 'users.name AS user_name', 'users.id AS registered')
                ->selectRaw('IF(NOW() < DATE_ADD(i1il4_comments_items.created, INTERVAL 15 MINUTE), 1, 0) AS edit')
                ->leftJoin('users', 'i1il4_comments_items.user_id', '=', 'users.id')
                ->where('i1il4_comments_items.object_group', $objectGroup)
                ->where('i1il4_comments_items.object_id', $objectId)
                ->orderBy('i1il4_comments_items.created', 'desc')
                ->paginate(10);
        } else {
            $items = self::select('i1il4_comments_items.*', 'i1il4_comments_items.username AS guest_name', 'users.name AS user_name', 'users.id AS registered')
                ->selectRaw('IF(NOW() < DATE_ADD(i1il4_comments_items.created, INTERVAL 15 MINUTE), 1, 0) AS edit')
                ->leftJoin('users', 'i1il4_comments_items.user_id', '=', 'users.id')
                ->where('i1il4_comments_items.object_group', $objectGroup)
                ->where('i1il4_comments_items.object_id', $objectId)
                ->where('i1il4_comments_items.status', '1')
                ->orderBy('i1il4_comments_items.created', 'desc')
                ->paginate(10);
        }

        if ($items->isNotEmpty()) {
            $bad = $neutrally = $good = 0;
            $n = $items->total();
            $items->each(function ($item) use (&$good, &$neutrally, &$bad, &$n) {
                // Добавляем номер отзыва к каждой записи
                $item->n = $n--;
                if ($item->rate > 3) {
                    $good++;
                } elseif ($item->rate === 3) {
                    $neutrally++;
                } else {
                    $bad++;
                }
            });

            $items[0]->blacklist = Blacklist::getBlacklist($request->ip());
            $items[0]->neutrally = $neutrally;
            $items[0]->bad = $bad;
            $items[0]->good = $good;
        }

        return $items;
    }
}
