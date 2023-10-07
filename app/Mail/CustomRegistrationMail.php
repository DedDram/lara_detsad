<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $verificationUrl;

    public function __construct($verificationUrl)
    {
        $this->verificationUrl = $verificationUrl;
    }

    public function build()
    {
        return $this->view('mail.registration')
            ->subject('Добро пожаловать на наш сайт')
            ->with(['verificationUrl' => $this->verificationUrl]);
    }
    public function via($notifiable)
    {
        // Переопределяем метод via и возвращаем массив с желаемыми способами доставки
        return ['mail'];
    }
}
