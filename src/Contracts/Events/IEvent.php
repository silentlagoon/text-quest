<?php

namespace App\Contracts\Events;

use App\Entities\Living\Player;

interface IEvent
{
    public function calculate(Player $player): void;
}