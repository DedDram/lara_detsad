<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentCreateRequest;
use App\Http\Requests\CreateCommentRequest;
use App\Models\Comments\Comments;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        if($request->has('task')){
            $task = (string) $request->input('task');
        }else{
            abort(404);
        }
        $data = [];

        if ($task == 'create') {
             $data = $this->comments->create($request);
        }
        if ($task == 'vote') {
            $data = $this->comments->vote($request);
        }
        if ($task == 'votes') {
            $data = $this->comments->votes($request);
        }
        if ($task == 'images') {
            $data = $this->comments->getImagesComment($request);
        }
        if ($task == 'addImage') {
            $data = $this->comments->addImage($request);
        }
        if ($task == 'removeImage') {
            $data = $this->comments->removeImage($request);
        }

        if (Auth::check()) {
            if (User::isAdmin()) {
                if ($task == 'publish') {
                    $data = $this->comments->publishItems($this->comment_id);
                }
                if ($task == 'unpublish') {
                    $data = $this->comments->unPublishItems($this->comment_id);
                }
                if ($task == 'remove') {
                    $data = $this->comments->remove($this->comment_id);
                }
                if ($task == 'blacklist') {
                    $data = $this->comments->blacklist($this->comment_id);
                }
            }
            if ($task == 'unsubscribe') {
                $data = $this->comments->unsubscribe($this->object_group, $this->object_id, Auth::id());
            }
            if ($task == 'edit') {
                $data = $this->comments->edit($this->comment_id, $request);
            }
        }

        return response()->json($data);
    }

}
