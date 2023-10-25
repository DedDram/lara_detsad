<?php

namespace App\Models\Comments;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;

class Comments extends Model
{
    protected $table = 'i1il4_comments_items';
    protected $primaryKey = 'id';
    protected $fillable = ['object_group', 'object_id', 'created', 'ip', 'user_id', 'rate', 'country', 'status', 'username', 'email', 'isgood', 'ispoor', 'description', 'images'];

    protected object $user;

    protected int $user_id;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Получаем текущего авторизованного пользователя (если он есть)
        if (Auth::check()) {
            $this->user = Auth::user();
            $this->user_id = Auth::id();
        } else {
            $this->user_id = 0;
        }

        $this->dir = 'public/images/comments';
    }

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

    private static function getBlacklist($request): bool
    {
        $ip = $request->ip();
        $blacklist = DB::table('i1il4_comments_blacklist')
            ->select(DB::raw('COUNT(*) as ban'))
            ->where('ip', '=', $ip)
            ->where('created', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 1 DAY)'))
            ->first();
        if (!empty($blacklist->ban)) {
            return true;
        } else {
            return false;
        }
    }

    public function create($request): array
    {
        $rate = (int)$request->input('star');
        if (empty($this->user)) {
            $username = self::input($request->input('username'));
            $email = (string)$request->input('email');
        } else {
            $username = $this->user->name;
            $email = $this->user->email;
        }
        if (!empty($request->input('subscribe'))) {
            $subscribe = $request->input('subscribe');
        }
        $description = (string)$request->input('description');
        $attach = (string)$request->input('attach');

        // Проголосовать можно один раз
        $temp = $this->getRate($request);
        if (empty($temp->itog) && ($rate < 1 || $rate > 5)) {
            return array(
                'status' => 2,
                'msg' => 'Вы не проголосовали! Выберите оценку отзыва.'
            );
        }
        // Проверка отзыва
        $description = self::_clearComment($description);

        // Фильтр
        $username = self::input($username);
        $description = self::input($description);
        //удаляем <br> на конце отзыва
        $description = preg_replace('~^(.*)(<br>|<br />|<br/>){1,}$~msU', '$1', $description);
        //удаляем <br> в начале отзыва
        $description = preg_replace('~^(<br>|<br />|<br/>){1,}(.*)$~ms', '$2', $description);

        //try {
            $item = [
                'created' => now(),
                'object_group' => $request->input('object_group'),
                'object_id' => $request->input('object_id'),
                'user_id' => $this->user_id,
                'ip' => $request->ip(),
                'username' => $username,
                'rate' => $rate,
                'description' => $description,
                'email' => $email,
            ];
            self::insert($item);
            // Получить последний вставленный ID
            $item_id = DB::getPdo()->lastInsertId();

            DB::table('i1il4_comments_images')
                ->where('item_id', 0)
                ->where('attach', $attach)
                ->update(['item_id' => $item_id]);

            DB::table('i1il4_comments_items')
                ->where('id', $item_id)
                ->update([
                    'images' => DB::table('i1il4_comments_images')
                        ->where('item_id', $item_id)
                        ->count()
                ]);
        /*} catch (QueryException $e) {
            return array(
                'status' => 2,
                'msg' => 'Удалите из отзыва специальные символы (смайлики и т.п.), оставьте просто текст.'
            );
        }*/
        if (!empty($this->user_id)) {
            // Публикуем
            self::publishItems($item_id);
            // Подписываемся
            if (!empty($subscribe)) {
                self::subscribe($request->input('object_group'), $request->input('object_id'), $this->user_id);
            }
        }
        // Уведомление админу
        self::setNotification($request->input('object_group'), $request->input('object_id'), $item_id, 2);

        if (!empty($this->user_id)) {
            return array(
                'status' => 1,
                'msg' => 'Спасибо, Ваш отзыв опубликован, чтобы увидеть его - перезагрузите страницу'
            );
        } else {
            return array(
                'status' => 1,
                'msg' => 'Спасибо, Ваш отзыв будет добавлен после проверки модератором'
            );
        }
    }

    public function edit(int $comment_id, Request $request): array
    {
        $description = (string)$request->input('description');
        $attach = (string)$request->input('attach');

        $result = DB::table('i1il4_comments_items')
            ->select('*')
            ->where('id', '=', $comment_id)
            ->where('user_id', '=', $this->user_id)
            ->where(DB::raw('DATE_ADD(created, INTERVAL 15 MINUTE)'), '>', 'NOW()')
            ->first();

        if ($result !== null || User::isAdmin()) {
            // Проверка отзыва
            $description = self::_clearComment($description);
            $description = self::input($description);

            DB::table('i1il4_comments_items')
                ->where('id', '=', $comment_id)
                ->update(['description' => $description]);
            DB::table('i1il4_comments_images')
                ->where('item_id', '=', 0)
                ->where('attach', '=', $attach)
                ->update(['item_id' => $comment_id]);
            DB::table('i1il4_comments_items')
                ->where('id', '=', $comment_id)
                ->update(['images' => DB::table('i1il4_comments_images')->where('item_id', '=', $comment_id)->count()]);
            return array(
                'status' => 1,
                'msg' => 'Спасибо, ваш отзыв сохранен, перезагрузите страницу.'
            );
        }
        return array(
            'status' => 2,
            'msg' => 'Ошибка сохранения!'
        );
    }

    public function unPublishItems(int $comment_id): array
    {
        if (!empty($comment_id)) {
            $items = DB::table('i1il4_comments_items')
                ->select('*')
                ->where('id', '=', $comment_id)
                ->first();
            self::_clearNotifications($comment_id);
            // Снимаем с публикации
            DB::table('i1il4_comments_items')
                ->where('id', '=', $comment_id)
                ->update(['status' => 0]);

            if (!empty($items)) {
                foreach ($items as $item) {
                    if (!empty($item->status)) {
                        // Пересчитываем рейтинг
                        self::_rate($item->object_group, $item->object_id, '-' . $item->rate);
                    }
                }
            }
            return array(
                'msg' => 'Комментарий снят с публикации'
            );
        }
        return array(
            'msg' => 'Ошибка снятия с публикации'
        );
    }

    public function publishItems(int $comment_id): array
    {
        if (!empty($comment_id)) {
            // Проверка на плохие слова
            self::parseCurseWords($comment_id);

            // Публикуем
            DB::table('i1il4_comments_items')
                ->where('id', '=', $comment_id)
                ->update(['status' => 1]);
            $item = DB::table('i1il4_comments_items')
                ->select('*')
                ->where('id', '=', $comment_id)
                ->first();
            if (!empty($items)) {
                // Пересчитываем рейтинг
                self::_rate($item->object_group, $item->object_id, '+' . $item->rate);
                // Добавляем рассылку
                self::setNotification($item->object_group, $item->object_id, $item->id, 1);
                // Добавляем страницу на переобход роботом
                $temp = self::getItem($item->object_group, $item->object_id);
                self::YandexWebmasterOverride(env('APP_URL') . $temp['url']);
            }
            return array(
                'msg' => 'Комментарий опубликован'
            );
        }
        return array(
            'msg' => 'Ошибка при публикации'
        );
    }

    public function blacklist(int $comment_id): array
    {
        if (!empty($comment_id)) {
            $comment = DB::table('i1il4_comments_items')
                ->where('id', $comment_id)
                ->select('ip')
                ->first();
            if($comment->ip !== null){
                DB::table('i1il4_comments_blacklist')
                    ->updateOrInsert(
                        ['ip' => $comment->ip],
                        ['created' => now()]
                    );
                return array(
                    'msg' => 'IP добавлен в черный список'
                );
            }
        }
        return array(
            'msg' => 'Ошибка добавления IP добавлен в черный список'
        );
    }

    public function remove(int $comment_id): array
    {
        if (!empty($comment_id)) {
            $items = DB::table('i1il4_comments_items')
                ->select('*')
                ->where('id', $comment_id)
                ->first();
            // Удаляем изображения
            if (!empty($items->images)) {
                self::_clearImages($comment_id);
            }
            // Чистим рассылку
            self::_clearNotifications($comment_id);

            // Удаляем комментарий
            DB::table('i1il4_comments_items')
                ->where('id', $comment_id)
                ->delete();

            if (!empty($items)) {
                foreach ($items as $item) {
                    if (!empty($item->status)) {
                        // Пересчитываем рейтинг
                        self::_rate($item->object_group, $item->object_id, '-' . $item->rate);
                    }
                }
            }
            return array(
                'msg' => 'Комментарий удален'
            );
        }
        return array(
            'msg' => 'Ошибка удаления'
        );
    }

    private function subscribe($object_group, $object_id, $user_id)
    {
        DB::table('i1il4_comments_subscribers')->updateOrInsert(
            [
                'object_group' => $object_group,
                'object_id' => $object_id,
                'user_id' => $user_id,
                'created' => now(),
            ]
        );
    }

    private function getRate($request): ?object
    {
        if (!empty($this->user)) {
            return DB::table('i1il4_comments_items')
                ->select(DB::raw('COUNT(*) as itog'))
                ->where('object_group', '=', $request->input('object_group'))
                ->where('object_id', '=', $request->input('object_id'))
                ->where('user_id', '=', $this->user_id)
                ->first();
        } else {
            return DB::table('i1il4_comments_items')
                ->select(DB::raw('COUNT(*) as itog'))
                ->where('object_group', '=', $request->input('object_group'))
                ->where('object_id', '=', $request->input('object_id'))
                ->where('ip', '=', $this->ip)
                ->where(DB::raw('DATE_ADD(created, INTERVAL 1 DAY)'), '>', now())
                ->first();
        }
    }

    private function input($text): string
    {
        //удаляем эмодзи
        $text = self::removeEmoji($text);
        $text = strip_tags($text, array("<br>", "\r", "\n", "<br/>", "<br><blockquote>"));
        // переносы
        $text = str_replace("\r\n", '<br>', $text);
        // пробелы
        $text = str_replace('&nbsp;', ' ', $text);
        $text = preg_replace("/\s{2,}/", ' ', $text);
        $text = preg_replace("~</blockquote>(<br>)+~", '</blockquote>', $text);
        $text = preg_replace('/(!{1,}|\.{1,}|,{1,}|\?{1,})(\S)/', '$1 $2', $text);
        $text = preg_replace('/(\s)(!|\.|,|\?)/', '$2', $text);
        return trim($text);
    }

    private function removeEmoji($text): string
    {
        return preg_replace('/[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0077}\x{E006C}\x{E0073}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0073}\x{E0063}\x{E0074}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0065}\x{E006E}\x{E0067}\x{E007F})|[\x{1F3F4}](?:\x{200D}\x{2620}\x{FE0F})|[\x{1F3F3}](?:\x{FE0F}\x{200D}\x{1F308})|[\x{0023}\x{002A}\x{0030}\x{0031}\x{0032}\x{0033}\x{0034}\x{0035}\x{0036}\x{0037}\x{0038}\x{0039}](?:\x{FE0F}\x{20E3})|[\x{1F441}](?:\x{FE0F}\x{200D}\x{1F5E8}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F468})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F468})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B0})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2640}\x{FE0F})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2642}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2695}\x{FE0F})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FF})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FE})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FD})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FC})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FB})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FA}](?:\x{1F1FF})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1FA}](?:\x{1F1FE})|[\x{1F1E6}\x{1F1E8}\x{1F1F2}\x{1F1F8}](?:\x{1F1FD})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F9}\x{1F1FF}](?:\x{1F1FC})|[\x{1F1E7}\x{1F1E8}\x{1F1F1}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1FB})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1FB}](?:\x{1F1FA})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FE}](?:\x{1F1F9})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FA}\x{1F1FC}](?:\x{1F1F8})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F7})|[\x{1F1E6}\x{1F1E7}\x{1F1EC}\x{1F1EE}\x{1F1F2}](?:\x{1F1F6})|[\x{1F1E8}\x{1F1EC}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}](?:\x{1F1F5})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EE}\x{1F1EF}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1F8}\x{1F1F9}](?:\x{1F1F4})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1F3})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F4}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FF}](?:\x{1F1F2})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F1})|[\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FD}](?:\x{1F1F0})|[\x{1F1E7}\x{1F1E9}\x{1F1EB}\x{1F1F8}\x{1F1F9}](?:\x{1F1EF})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EB}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F3}\x{1F1F8}\x{1F1FB}](?:\x{1F1EE})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1ED})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1EC})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F9}\x{1F1FC}](?:\x{1F1EB})|[\x{1F1E6}\x{1F1E7}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FB}\x{1F1FE}](?:\x{1F1EA})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1E9})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FB}](?:\x{1F1E8})|[\x{1F1E7}\x{1F1EC}\x{1F1F1}\x{1F1F8}](?:\x{1F1E7})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F6}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}\x{1F1FF}](?:\x{1F1E6})|[\x{00A9}\x{00AE}\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}-\x{21AA}\x{231A}-\x{231B}\x{2328}\x{23CF}\x{23E9}-\x{23F3}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}-\x{25AB}\x{25B6}\x{25C0}\x{25FB}-\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}-\x{2615}\x{2618}\x{261D}\x{2620}\x{2622}-\x{2623}\x{2626}\x{262A}\x{262E}-\x{262F}\x{2638}-\x{263A}\x{2640}\x{2642}\x{2648}-\x{2653}\x{2660}\x{2663}\x{2665}-\x{2666}\x{2668}\x{267B}\x{267E}-\x{267F}\x{2692}-\x{2697}\x{2699}\x{269B}-\x{269C}\x{26A0}-\x{26A1}\x{26AA}-\x{26AB}\x{26B0}-\x{26B1}\x{26BD}-\x{26BE}\x{26C4}-\x{26C5}\x{26C8}\x{26CE}-\x{26CF}\x{26D1}\x{26D3}-\x{26D4}\x{26E9}-\x{26EA}\x{26F0}-\x{26F5}\x{26F7}-\x{26FA}\x{26FD}\x{2702}\x{2705}\x{2708}-\x{270D}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2728}\x{2733}-\x{2734}\x{2744}\x{2747}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2763}-\x{2764}\x{2795}-\x{2797}\x{27A1}\x{27B0}\x{27BF}\x{2934}-\x{2935}\x{2B05}-\x{2B07}\x{2B1B}-\x{2B1C}\x{2B50}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{1F004}\x{1F0CF}\x{1F170}-\x{1F171}\x{1F17E}-\x{1F17F}\x{1F18E}\x{1F191}-\x{1F19A}\x{1F201}-\x{1F202}\x{1F21A}\x{1F22F}\x{1F232}-\x{1F23A}\x{1F250}-\x{1F251}\x{1F300}-\x{1F321}\x{1F324}-\x{1F393}\x{1F396}-\x{1F397}\x{1F399}-\x{1F39B}\x{1F39E}-\x{1F3F0}\x{1F3F3}-\x{1F3F5}\x{1F3F7}-\x{1F3FA}\x{1F400}-\x{1F4FD}\x{1F4FF}-\x{1F53D}\x{1F549}-\x{1F54E}\x{1F550}-\x{1F567}\x{1F56F}-\x{1F570}\x{1F573}-\x{1F57A}\x{1F587}\x{1F58A}-\x{1F58D}\x{1F590}\x{1F595}-\x{1F596}\x{1F5A4}-\x{1F5A5}\x{1F5A8}\x{1F5B1}-\x{1F5B2}\x{1F5BC}\x{1F5C2}-\x{1F5C4}\x{1F5D1}-\x{1F5D3}\x{1F5DC}-\x{1F5DE}\x{1F5E1}\x{1F5E3}\x{1F5E8}\x{1F5EF}\x{1F5F3}\x{1F5FA}-\x{1F64F}\x{1F680}-\x{1F6C5}\x{1F6CB}-\x{1F6D2}\x{1F6E0}-\x{1F6E5}\x{1F6E9}\x{1F6EB}-\x{1F6EC}\x{1F6F0}\x{1F6F3}-\x{1F6F9}\x{1F910}-\x{1F93A}\x{1F93C}-\x{1F93E}\x{1F940}-\x{1F945}\x{1F947}-\x{1F970}\x{1F973}-\x{1F976}\x{1F97A}\x{1F97C}-\x{1F9A2}\x{1F9B0}-\x{1F9B9}\x{1F9C0}-\x{1F9C2}\x{1F9D0}-\x{1F9FF}]/u', '', $text);
    }

    public function unsubscribe($object_group, $object_id, $user_id): array
    {
        DB::table('i1il4_comments_subscribers')
            ->where('object_group', '=', $object_group)
            ->where('object_id', '=', $object_id)
            ->where('user_id', '=', $user_id)
            ->delete();
        return array(
            'msg' => 'Вы отписались от уведомлений о новых комментариях'
        );
    }

    private function setNotification($object_group, $object_id, $item_id, $type)
    {
        $temp = self::getItem($object_group, $object_id);
        $title = $temp['title'];
        $url = $temp['url'];

        if ($type == 1) {
            DB::table('i1il4_comments_cron')
                ->select('item_id', 'user_id', 'type', 'title', 'url')
                ->from('i1il4_comments_subscribers')
                ->where('object_group', $object_group)
                ->where('object_id', $object_id)
                ->updateOrInsert(
                    [
                        'item_id' => $item_id,
                        'user_id' => DB::raw('user_id'),
                        'type' => $type,
                        'title' => $title,
                        'url' => $url
                    ]
                );
        }
        if ($type == 2 || $type == 3) {
            DB::table('i1il4_comments_cron')
                ->updateOrInsert(
                    [
                        'item_id' => $item_id,
                        'type' => $type,
                        'title' => $title,
                        'url' => $url
                    ]
                );
        }
        self::clearCache($url);
    }

    private function _clearComment($comment): string
    {
        $comment = preg_replace("/\<blockquote\>(.*)\<\/blockquote\>/", '', $comment);
        $comment = strip_tags($comment, array("<br>", "\r", "\n", "<br/>"));
        return trim($comment);
    }

    private function YandexWebmasterOverride($url)
    {
        $item = DB::table('i1il4_comments_webmaster')
            ->select('*')
            ->where('id', '=', 1)
            ->first();

        if (!empty($item->user_id) && !empty($item->host_id) && !empty($item->access_token)) {
            $data = json_encode(array(
                "url" => $url
            ));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.webmaster.yandex.net/v4/user/" . $item->user_id . "/hosts/" . $item->host_id . "/recrawl/queue/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: OAuth " . $item->access_token, "Accept: application/json", "Content-type: application/json"));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $response = curl_exec($ch);
            curl_close($ch);
        }
    }

    private function parseCurseWords(int $comment_id)
    {
        $values = array();
        $words = array();
        $items = DB::table('i1il4_comments_cursewords')
            ->select('*')
            ->get();

        if (!empty($items)) {
            foreach ($items as $item) {
                $words[] = $item->text;
            }
        }

        if (!empty($ids)) {
            $items = DB::table('i1il4_comments_items')
                ->select('id', 'description')
                ->where('id', '=', $comment_id)
                ->first();
            if (!empty($items)) {
                foreach ($items as $item) {
                    foreach ($words as $word) {
                        if (strpos($item['description'], $word)) {
                            $description = preg_replace('/([^\d\w]|^)' . $word . '([^\d\w]|$)/iu', '$1***$2', $item['description']);
                            $values[] = "('{$item['id']}', '{$description}')";
                        }
                    }
                }
            }
        }

        if (!empty($values)) {
            DB::table('i1il4_comments_items')
                ->upsert($values, ['id'], ['description' => DB::raw('VALUES(description)')]);
        }
    }

    private function _clearImages(int $comment_id): void
    {
        if (!empty($comment_id)) {
            $images = DB::table('i1il4_comments_images')
                ->select('*')
                ->where('item_id', '=', $comment_id)
                ->get();
            if (!empty($images)) {
                foreach ($images as $image) {
                    unlink(storage_path('app/' . $this->dir . '/' . $image->thumb));
                    unlink(storage_path('app/' . $this->dir . '/' . $image->original));
                }
                DB::table('i1il4_comments_images')
                    ->where('item_id', '=', $comment_id)
                    ->delete();
            }
        }
    }

    private function _clearNotifications(int $comment_id)
    {
        if (!empty($comment_id)) {
            DB::table('i1il4_comments_cron')
                ->where('item_id', '=', $comment_id)
                ->delete();
        }
    }

    private function _rate(string $object_group, int $object_id, int $value)
    {
        $rate = 0;
        $vote = 0;
        if ($value > 0) {
            $rate = $value;
            $vote = 1;
        }
        if ($value < 0) {
            $rate = $value;
            $vote = -1;
        }

        if ($object_group == 'com_detsad') {
            DB::table('i1il4_detsad_items')
                ->where('id', $object_id)
                ->update([
                    'rate' => DB::raw("rate + $rate"),
                    'vote' => DB::raw("vote + $vote"),
                    'average' => DB::raw("CASE WHEN vote = 0 THEN 0 ELSE vote/(vote+10)*rate/vote+10/(vote+10)*3.922 END"),
                    'comments' => DB::raw("(SELECT COUNT(*) FROM i1il4_comments_items WHERE object_id = $object_id AND object_group = '$object_group' AND status = 1)"),
                ]);
        }
        if ($object_group == 'com_content') {
            DB::table('i1il4_content')
                ->where('id', $object_id)
                ->update([
                    'rate' => DB::raw("rate + $rate"),
                    'vote' => DB::raw("vote + $vote"),
                    'average' => DB::raw("CASE WHEN vote = 0 THEN 0 ELSE vote/(vote+10)*rate/vote+10/(vote+10)*3.922 END"),
                    'comments' => DB::raw("(SELECT COUNT(*) FROM i1il4_comments_items WHERE object_id = $object_id AND object_group = '$object_group' AND status = 1)"),
                ]);
        }
    }

    private function getItem(string $object_group, int $object_id): ?array
    {
        $item = [];
        if ($object_group == 'com_detsad') {
            $detsad = DB::table('i1il4_detsad_items  AS t1')
                ->select([
                    't1.*',
                    DB::raw('CONCAT_WS("-", t1.id, t1.alias) AS item_alias'),
                    DB::raw('CONCAT_WS("-", t2.id, t2.alias) AS category_alias'),
                    DB::raw('CONCAT_WS("-", t3.id, t3.alias) AS section_alias')
                ])
                ->join('i1il4_detsad_categories AS t2', 't1.category_id', '=', 't2.id')
                ->join('i1il4_detsad_sections AS t3', 't1.section_id', '=', 't3.id')
                ->where('t1.id', '=', $object_id)
                ->first();
            $item['title'] = $detsad->title;
            $item['url'] = '/' . $detsad->category_alias . '/' . $detsad->item_alias;
        }

        if ($object_group == 'com_content') {
            $content = DB::table('i1il4_content  AS t1')
                ->select('t1.title', 't1.alias as contentAlias', 't2.alias as section_alias')
                ->join('i1il4_categories AS t2', 't2.id', '=', 't1.catid')
                ->where('t1.id', '=', $object_id)
                ->first();

            $item['title'] = preg_replace('@ - отзывы.*@smi', '', $content->title);

            $item['url'] = '/' . $content->section_alias . '/' . $object_id . '/' . $content->contentAlias;
        }
        return $item;
    }

    public function vote($request): array
    {
        $item_id = (int) $request->input('id');
        $value = (string) $request->input('value');

        $result = DB::table('i1il4_comments_votes')
            ->select(DB::raw('*'))
            ->where('item_id', '=', $item_id)
            ->where('ip', '=', $request->ip())
            ->first();;
        if ($result === null) {
            if ($value == 'up') {
                DB::table('i1il4_comments_votes')
                    ->insert(['item_id' => $item_id, 'ip' => $request->ip(), 'value' => 1]);
            }
            if ($value == 'down') {
                DB::table('i1il4_comments_votes')
                    ->insert(['item_id' => $item_id, 'ip' => $request->ip(), 'value' => -1]);
            }

            DB::table('i1il4_comments_items')
                ->where('id', $item_id)
                ->update([
                    'isgood' => DB::table('i1il4_comments_votes')
                        ->where('item_id', $item_id)
                        ->where('value', '>', 0)
                        ->sum('value'),
                    'ispoor' => DB::table('i1il4_comments_votes')
                        ->where('item_id', $item_id)
                        ->where('value', '<', 0)
                        ->sum(DB::raw('-value')),
                ]);

            return array(
                'msg' => 'Спасибо, Ваш голос принят!'
            );
        } else {
            return array(
                'msg' => 'Повторное голосование не учитывается!'
            );
        }
    }

    public function votes($request): ?object
    {
        $votes = (string)$request->input('votes');
        $object_id = (int)$request->input('objectid');
        $object_group = (string)$request->input('objectgroup');
        if (empty($object_group)) {
            $object_group = 'com_content';
        }

        $query = DB::table('i1il4_comments_items as t1')
            ->select('t1.*', 't1.username AS guest_name', 't2.name AS user_name', 't2.id AS registered')
            ->leftJoin('users AS t2', 't1.user_id', '=', 't2.id')
            ->where('t1.object_group', '=', $object_group)
            ->where('t1.object_id', '=', $object_id)
            ->where('t1.status', '=', 1);

        if ($votes == 'good') {
            $query->where('t1.rate', '>=', 4);
        } elseif ($votes == 'neutrally') {
            $query->where(function ($query) {
                $query->where('t1.rate', 3)->orWhere('t1.rate', '');
            });
        } elseif ($votes == 'bad') {
            $query->where('t1.rate', '<=', 2)->where('t1.rate', '!=', '');
        }

        return $query->orderBy('t1.created', 'DESC')->get();
    }

    /**
     * Показ и загрузка фото к отзывам
     */
    public function getImagesComment($request): ?object
    {
        $item_id = (int) $request->input('id');
        return DB::table('i1il4_comments_images')
            ->select('*')
            ->where('item_id', $item_id)
            ->orderBy('id', 'ASC')
            ->get();
    }

    public function addImage(Request $request): array
    {

        if ($request->hasFile('file') && $request->has('attach')) {
            $file = $request->file('file');
            $attach = $request->input('attach');

            if ($file->isValid()) {
                $originalFileName = md5(uniqid(rand(), 1)) . '.' . $file->getClientOriginalExtension();
                $thumbFileName = md5(uniqid(rand(), 1)) . '_thumb.' . $file->getClientOriginalExtension();

                // Сохраняем оригинальное изображение
                $file->storeAs($this->dir, $originalFileName);
                // Создаем и обрабатываем миниатюру
                $image = Image::make(storage_path('app/' . $this->dir . '/' . $originalFileName));
                $image->resize(200, 200, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $image->save(storage_path('app/' . $this->dir . '/' . $thumbFileName));

                // Сохраняем информацию в базе данных
                DB::table('i1il4_comments_images')->insert([
                    'created' => now(),
                    'attach' => $attach,
                    'thumb' => $thumbFileName,
                    'original' => $originalFileName,
                ]);

                // Получаем ID последней вставки
                $id = DB::getPdo()->lastInsertId();

                return [
                    'status' => 1,
                    'attach' => $attach,
                    'thumb' => $thumbFileName,
                    'id' => $id,
                ];
            } else {
                return [
                    'status' => 2,
                    'msg' => 'Ошибка при загрузке файла.',
                ];
            }
        } else {
            return [
                'status' => 2,
                'msg' => 'Укажите файл для загрузки и параметр attach.',
            ];
        }
    }

    public function removeImage(): array
    {
        $id_img = (int)$_POST['id_img'];
        $attach = (string)$_POST['attach'];

        $item = DB::table('i1il4_comments_images')
            ->select('*')
            ->where('id', $id_img)
            ->first();

        if (!empty($user_id) && !empty($item->item_id)) {
            $temp = DB::table('i1il4_comments_images')
                ->where('id', $item->item_id)
                ->where('user_id', $user_id)
                ->count();
        } else {
            $temp = DB::table('i1il4_comments_images')
                ->where('item_id', 0)
                ->where('id', $id_img)
                ->where('attach', $attach)
                ->count();
        }

        if (!empty($temp)) {
            self::removeImages($id_img, $item);

            return array(
                'status' => 1
            );
        }
        return array(
            'status' => 2,
            'msg' => 'Ошибка удаления изображения'
        );
    }

    private function removeImages(int $id_img, object $item): void
    {
        unlink(storage_path('app/' . $this->dir . '/' . $item->thumb));
        unlink(storage_path('app/' . $this->dir . '/' . $item->original));

        DB::table('i1il4_comments_images')
            ->where('id', $id_img)
            ->delete();
        DB::table('i1il4_comments_items')
            ->where('id', $item->item_id)
            ->update(['images' => DB::table('i1il4_comments_images')->where('item_id', $item->item_id)->count()]);
    }

    /**
     * Функция очистки кеша Nginx
     * @param string $value
     * @return void
     */
    static function clearCache(string $value): void
    {
        if (!empty($value)) {
            $data = parse_url($value);
            $filename = md5('GET|detskysad.com|' . $data['path']);
            if (file_exists('/var/cache/nginx/detsad/' . substr($filename, -1) . '/' . substr($filename, -3, 2) . '/' . $filename)) {
                unlink('/var/cache/nginx/detsad/' . substr($filename, -1) . '/' . substr($filename, -3, 2) . '/' . $filename);
            }
        }
    }

}
