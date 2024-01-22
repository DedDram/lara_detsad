<?php

namespace App\Listeners;

use App\Events\ReviewAdded;
use Illuminate\Support\Facades\Broadcast;

class NotifyAdminAboutReview
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReviewAdded $event): void
    {
        $sadikName = $event->sadikName;
        $sadikUrl = $event->sadikUrl;

        // Отправка события на фронтенд
        Broadcast::event('admin-channel', new ReviewAdded($sadikName, $sadikUrl));
    }
}
