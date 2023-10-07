<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $subject;
    public string $message;
    public array $data;

    public function __construct($subject, $message, $data)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->data = $data;
    }

    public function build()
    {
        return $this->markdown('mail.agentNotification')
            ->subject($this->subject);
    }
}
