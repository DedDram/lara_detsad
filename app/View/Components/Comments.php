<?php

namespace App\View\Components;
use App\Models\User;
use Illuminate\View\Component;
class Comments extends Component
{
    public string $object_group;
    public ?int $object_id;

    public ?object  $items;
    public ?int $countComments;
    public ?int $good;
    public ?int $neutrally;
    public ?int $bad;
    public ?float $procentGood;
    public ?float $procentNeutrally;
    public ?float $procentBad;
    public ?int $modulePosition;
    public ?int $num;

    public ?User $user;

    public function __construct($objectGroup, $objectId, $items, $countComments, $good, $neutrally, $bad, $procentGood, $procentNeutrally, $procentBad, $modulePosition, $num, $user)
    {
        $this->object_group = $objectGroup;
        $this->object_id = $objectId;
        $this->items = $items;
        $this->countComments = $countComments;
        $this->good = $good;
        $this->neutrally = $neutrally;
        $this->bad = $bad;
        $this->procentGood = $procentGood;
        $this->procentNeutrally = $procentNeutrally;
        $this->procentBad = $procentBad;
        $this->modulePosition = $modulePosition;
        $this->num = $num;
        $this->user = $user;
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.comments');
    }
}
