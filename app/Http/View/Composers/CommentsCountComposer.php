<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Comments;

class CommentsCountComposer
{
    public function compose(View $view)
    {
        $totalCount = Comments::totalCommentsCount();
        $view->with('totalCount', $totalCount);
    }
}
