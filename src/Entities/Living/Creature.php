<?php

namespace App\Entities\Living;

use App\Contracts\Entities\ICreature;

class Creature implements ICreature
{
    const MIN_HIT_POINTS = 0;
    protected string $name;
    protected int $maxHitPoints;
    protected int $hitPoints;
    protected int $damage;
    protected string $fightColor;

    public function __construct(string $name, int $maxHitPoints, int $damage)
    {
        $this->name = $name;
        $this->maxHitPoints = $maxHitPoints;
        $this->hitPoints = $maxHitPoints;
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
        if ($hitPoints < static::MIN_HIT_POINTS) {
            $this->hitPoints = static::MIN_HIT_POINTS;
        } else {
            $this->hitPoints = $hitPoints;
        }
    }
    protected function getMaxHitPoints(): int
    {
        return $this->maxHitPoints;
    }
    public function getCreatureHitPointsPercentage(): float
    {
        return $this->getHitPoints() / ($this->getMaxHitPoints() / 100);
    }

    public function getFightColor(): string
    {
        return $this->fightColor;
    }

    protected function isDead(): bool
    {
        if ($this->hitPoints <= static::MIN_HIT_POINTS) {
            return true;
        }
        return false;
    }

    public function fight(Creature $monster, int $framesCounter): void
    {
        $everyTwoSeconds = (($framesCounter / 120) % 4) == 1;
        while (!$this->isDead() && !$monster->isDead() && $everyTwoSeconds) {
                $monster->setHitPoints($monster->getHitPoints() - $this->getDamage());
                $this->setHitPoints($this->getHitPoints() - $monster->getDamage());
                $framesCounter = 0;
            }
        }
}