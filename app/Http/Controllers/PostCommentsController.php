<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentCreateRequest;
use App\Models\Comments\Comments;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostCommentsController
{

    protected string $object_group;
    protected int $object_id;
    protected string $ip;
    protected string $email;
    protected string $comment_id;
    protected object $comments;

    function __construct(Request $request, Comments $comments)
    {
        $this->object_group = $request->filled('object_group') &&
        in_array($request->input('object_group'), ['com_content', 'com_detsad']) ?
            $request->input('object_group') : '';

        $this->object_id = $request->filled('object_id') ? (int) $request->input('object_id') : 0;
        $this->comment_id = $request->filled('item_id') ? (int) $request->input('item_id') : 0;
        $this->comments = $comments;
    }

    public function getResponse(CommentCreateRequest $request): JsonResponse
    {
        if($request->filled('task')){
            $task = (string) $request->input('task');
        }else{
            abort(404);
        }

        $data = match($task) {
            'create' => $this->comments->create($request),
            'vote' => $this->comments->vote($request),
            'votes' => $this->comments->votes($request),
            'images' => $this->comments->getImagesComment($request),
            'addImage' => $this->comments->addImage($request),
            'removeImage' => $this->comments->removeImage($request),
            default => null
        };

        if (Auth::check()) {
            if (User::isAdmin()) {
                $data = match ($task){
                    'publish' => $this->comments->publishItems($this->comment_id),
                    'unpublish' => $this->comments->unPublishItems($this->comment_id),
                    'remove' => $this->comments->remove($this->comment_id),
                    'blacklist' => $this->comments->blacklist($this->comment_id),
                };
            }
            $data = match ($task){
                'unsubscribe' => $this->comments->unsubscribe($this->object_group, $this->object_id, Auth::id()),
                'edit' => $this->comments->edit($this->comment_id, $request),
            };
        }
        return response()->json($data);
    }

}
