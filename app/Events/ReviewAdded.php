<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewAdded
{
    use Dispatchable, SerializesModels;

    public string $sadikName;
    public string $sadikUrl;

    public function __construct(string $sadikName, string $sadikUrl)
    {
        $this->sadikName = $sadikName;
        $this->sadikUrl = $sadikUrl;
    }
}
