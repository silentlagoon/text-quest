<?php

namespace App\Events;

use App\Entities\Living\Player;

class FightEvent
{
    protected array $fightEvents;
    public function __construct($fightEvents)
    {
        return $this->fightEvents = $fightEvents;
    }
    public function initiateFightEvent(Player $player): void
    {
        foreach ($this->fightEvents as $fightEvent) {
            foreach ($fightEvent as $monster) {
                $player->fight($monster);
            }
        }
    }
    public function getActors(): array
    {
        $actors = [];
        foreach ($this->fightEvents as $fightEvent) {
            foreach ($fightEvent as $monster) {
                $actors[] = $monster;
            }
        }
        return $actors;
    }
}