<?php

namespace App\View\Components;

use App\Models\Comments;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class CommentsCount extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $totalCount = Cache::remember('total_comments_count', now()->addMinutes(60), function () {
            return Comments::totalCommentsCount();
        });
        return view('components.comments-count', ['totalCount' => $totalCount]);
    }
}
