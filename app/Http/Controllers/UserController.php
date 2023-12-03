<?php

namespace App\Http\Controllers;

use App\Mail\AgentNotification;
use App\Models\Comments\Comments;
use App\Models\DetSad\CommentsSadik;
use App\Models\DetSad\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class UserController
{
    public function agent(Request $request)
    {

        if ($request->query('activation') && $request->query('user_id')) {
            $user = User::find($request->query('user_id')); // Найдем пользователя по его идентификатору
            $sadik = Item::find($user->sad_id);
            if ($user) {
                $user->status = 1; // Обновим поле status
                $user->save();
                $sadik->user_id_agent = $user->id; // Обновим поле user_id_agent в таблице садиков
                $sadik->save();
                $data = [
                    'url' => env('APP_URL'),
                ];

                $subject = 'Представитель Садика активирован';
                $message = 'Добро пожаловать на сайт DetskySad.com, ваш аккаунт представителя садика активирован.';
                $mail = new AgentNotification($subject, $message, $data);
                $mail->markdown('mail.agentNotification');
                Mail::to($user->email)->send($mail);

                return redirect(url('/'))->with('success', 'Представитель садика активирован');
            }
        }
        if ($request->query('delete') && $request->query('user_id')) {
            $user = User::find($request->query('user_id')); // Найдем пользователя по его идентификатору
            if ($user) {
                $data = [
                    'text' => 'Мы не смогли идентифицировать ваш e-mail с нужным садиком (не нашли его на сайте) и вынуждены удалить представителя. При регистрации вы должны указать e-mail, который указан на официальном сайте садика.',
                    'url' => env('APP_URL'),
                ];
                Mail::send('mail.agentNotification', $data, function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Представитель садика удален с портала DetskySad.com');
                });
                $user->delete(); // Удаляем
                return redirect(url('/'))->with('error', 'Представитель садика удален');
            }
        }
    }

    public function profile(): View
    {
        $allComments = CommentsSadik::getAllComments();
        if(!empty(Auth::user()->sad_id)){
            $agent = Item::getUrlSadik(Auth::user()->sad_id);
        }else{
            $agent = '';
        }
        $comments = Comments::getCommentUser(Auth::id());
        return view('users.profile', [
            'title' => 'Личный кабинет',
            'user' => Auth::user(),
            'agent' => $agent, 'comments' => $comments,
            'allComments' => $allComments
        ]);
    }

    public function updateProfile(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = User::find(Auth::id());

        if ($user) {
            $user->name = $request->input('name');
            if(!empty($request->input('password'))){
                $user->password = Hash::make($request->input('password'));
            }
            $user->save();
            return redirect()->back()->with('success', 'Данные обновлены');
        }else{
            return redirect()->back()->with('error', 'Пользователь не найден');
        }
    }
}
