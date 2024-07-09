<?php

namespace App\Contracts\Entities;

use App\Entities\Living\Creature;

interface ICreature
{
    public function getName(): string;
    public function getHitPoints(): int;
    public function getDamage(): int;
    public function setHitPoints(int $hitPoints): void;
    public function fight(Creature $monster, int $framesCounter): void;
    public function getFightColor(): string;
    public function getCreatureHitPointsPercentage(): float;
}