<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AfterCreatCommentNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * Отправка уведомлений юзерам подписанным на отзывы
         */
        $items = DB::table('i1il4_comments_cron as t1')
            ->select('t1.id AS ids', 't1.type', 't1.title', 't1.url', 't2.id', 't2.images', 't2.status',
                't2.object_group', 't2.object_id', 't2.ip', 't2.user_id', 't2.country', 't2.created', 't2.description',
                DB::raw('IF(t2.user_id > 0, t3.name, t2.username) AS username'),
                DB::raw('IF(t2.user_id > 0, t3.email, t2.email) AS useremail'), 't4.email')
            ->join('i1il4_comments_items as t2', 't1.item_id', '=', 't2.id')
            ->leftJoin('users as t3', 't2.user_id', '=', 't3.id')
            ->leftJoin('users as t4', 't1.user_id', '=', 't4.id')
            ->orderBy('t1.type', 'ASC')
            ->limit(5)
            ->get();

        $ids = array();

        if(!empty($items))
        {
            foreach($items as $item)
            {
                if($item->type == 1)
                {
                    $temp = self::user($item);

                    if(!empty($temp))
                    {
                        $ids[] = $item->ids;
                    }
                }

                if($item->type == 2 || $item->type == 3)
                {
                    $temp = self::admin($item);

                    if(!empty($temp))
                    {
                        $ids[] = $item->ids;
                    }
                }
            }
        }

        if(!empty($ids))
        {
            DB::table('i1il4_comments_cron')
                ->whereIn('id', $ids)
                ->delete();
        }
    }


    /**
     * Отправка письму уведомления юзеру подписанному на отзывы
     */
    private function user($item): bool
    {
        $data = [
            'item' => $item,
            'siteName' => config('app.url'),
        ];

        Mail::send('mail.userNotification', $data, function ($message) use ($item) {
            $message->to($item->email)
                ->subject('Новый отзыв: ' . $item->title);
        });

        return true;
    }


    /**
     * * Отправка письму уведомления админу
     */
    private function admin($item): bool
    {
        if($item->type == 2)
        {
            $title = 'Добавлен новый отзыв';
        }else{
            $title = 'Редактирование отзыва';
        }
        $item->country = (!empty($item->country)) ? $item->country : 'Страна не определена';

        if(!empty($item->images))
        {
            $images = DB::table('i1il4_comments_images')
                ->select('*')
                ->where('item_id', $item->id)
                ->get();
        }else{
            $images = null;
        }

        $data = [
            'item' => $item,
            'images' => $images,
            'siteName' => config('app.url')
        ];
        Mail::send('mail.adminNotification', $data, function ($message) use ($item, $title){
            $message->to(config('mail.from.address'))
                ->subject($title.': '.$item->title);
        });

        return true;
    }

}
