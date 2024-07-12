<?php

namespace App\Contracts\Events;

use App\Entities\Living\Player;

interface IEvent
{
    public function calculate(Player $player): void;
    public function isFinished(): bool;
    public function announceMessage(): string;
    public function confirmationMessage(): string;
}