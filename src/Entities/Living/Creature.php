<?php

namespace App\Entities\Living;

class Creature
{
    protected string $name;
    protected int $hitPoints;
    protected int $damage;

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
}