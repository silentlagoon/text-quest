<?php

namespace App\Entities\Living;

use App\Contracts\Entities\ICreature;

class Creature implements ICreature
{
    const MIN_HIT_POINTS = 0;
    protected static string $hh = 'child';
    protected string $name;
    protected int $hitPoints;
    protected int $damage;
    protected string $fightColor;

    public function __construct(string $name, int $hitPoints, int $damage)
    {
        $this->name = $name;
        $this->hitPoints = $hitPoints;
        $this->damage = $damage;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getHitPoints(): int
    {
        return $this->hitPoints;
    }

    /**
     * @return int
     */
    public function getDamage(): int
    {
        return $this->damage;
    }

    public function setHitPoints(int $hitPoints): void
    {
        if ($hitPoints < static::$hh) {
            $this->hitPoints = static::MIN_HIT_POINTS;
        } else {
            $this->hitPoints = $hitPoints;
        }
    }

    public function getFightColor(): string
    {
        return $this->fightColor;
    }

    protected function isDead(): bool
    {
        return $this->hitPoints === static::MIN_HIT_POINTS;
    }

    public function fight(Creature $monster): void
    {
        while (!$this->isDead() && !$monster->isDead()) {
            $monster->setHitPoints($monster->getHitPoints() - $this->getDamage());
            $this->setHitPoints($this->getHitPoints() - $monster->getDamage());
        }
    }
}