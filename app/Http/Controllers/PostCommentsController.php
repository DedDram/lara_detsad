<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentRequest;
use App\Models\Comments\Comments;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostCommentsController
{
    /** @var string */
    protected string $object_group;
    /** @var int */
    protected int $object_id;
    /** @var string */
    protected string $ip;

    /** @var string */
    protected string $email;
    /** @var string */
    protected string $comment_id;

    protected object $comments;

    function __construct(Request $request)
    {
        if ($request->has('object_group') && ($request->input('object_group') === 'com_content' || $request->input('object_group') === 'com_detsad')) {
            $this->object_group = $request->input('object_group');
        }
        if ($request->has('object_id') && !empty($request->input('object_id'))) {
            $this->object_id = (int) $request->input('object_id');
        }
        if ($request->has('item_id') && !empty($request->input('item_id'))) {
            $this->comment_id = (int) $request->input('item_id');
        }
    }

    public function getResponse(Request $request): \Illuminate\Http\JsonResponse
    {
        if($request->has('task')){
            $task = (string) $request->input('task');
        }else{
            abort(404);
        }
        $data = [];

        if ($task == 'create') {
            //валидация данных формы
            $validatorData = $this->validateUserData($request);
            if ($validatorData['status'] === 2) {
                return response()->json($validatorData);
            } else {
                $data = (new Comments)->create($request);
            }
        }
        if ($task == 'vote') {
            $data = (new Comments)->vote($request);
        }
        if ($task == 'votes') {
            $data = (new Comments)->votes($request);
        }
        if ($task == 'images') {
            $data = (new Comments)->getImagesComment($request);
        }
        if ($task == 'addImage') {
            $data = (new Comments)->addImage($request);
        }
        if ($task == 'removeImage') {
            $data = (new Comments)->removeImage($request);
        }

        if (Auth::check()) {
            if (User::isAdmin()) {
                if ($task == 'publish') {
                    $data = (new Comments)->publishItems($this->comment_id);
                }
                if ($task == 'unpublish') {
                    $data = (new Comments)->unPublishItems($this->comment_id);
                }
                if ($task == 'remove') {
                    $data = (new Comments)->remove($this->comment_id);
                }
                if ($task == 'blacklist') {
                    $data = (new Comments)->blacklist($this->comment_id);
                }
            }
            if ($task == 'unsubscribe') {
                $data = (new Comments)->unsubscribe($this->object_group, $this->object_id, Auth::id());
            }
            if ($task == 'edit') {
                $validatorData = $this->validateUserData($request);
                if ($validatorData['status'] === 2) {
                    return response()->json($validatorData);
                } else {
                    $data = (new Comments)->edit($this->comment_id, $request);
                }
            }
        }

        return response()->json($data);
    }

    public function createComment(CreateCommentRequest $request)
    {

    }


    public function validateUserData(Request $request): array
    {
        $rules = [
            'description' => 'required|string|min:100|latin_characters|no_spam_links',
        ];

        $messages = [
            'description.required' => 'Пожалуйста, введите текст отзыва',
            'description.min' => 'Минимальная длина отзыва - 100 символов',
            'description.latin_characters' => 'Отзывы на латинице запрещены',
            'description.no_spam_links' => 'Спам не пройдет!',
        ];

        // Если пользователь не аутентифицирован, включаем правила для имени и email
        if (!Auth::check()) {
            $rules['username'] = 'required';
            $rules['email'] = 'required|email';

            $messages['username.required'] = 'Пожалуйста, введите Ваше имя';
            $messages['email.required'] = 'Пожалуйста, введите E-mail';
            $messages['email.email'] = 'Пожалуйста, введите корректный E-mail';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return [
                'status' => 2,
                'msg' => $validator->errors()->first(),
            ];
        } else {
            return [
                'status' => 1
            ];
        }
    }
}
