<?php

namespace App\View\Components;

use App\Models\Comments\Blacklist;
use Illuminate\View\Component;

class Comments extends Component
{
    protected string $ip;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->ip = $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $blacklist = Blacklist::getBlacklist($this->ip);
        return view('components.comments');
    }
}
