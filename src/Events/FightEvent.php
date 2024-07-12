<?php

namespace App\Events;

use App\Contracts\Entities\ICreature;
use App\Contracts\Events\IEvent;
use App\DTO\CreatureNameDeltaPosition;
use App\Entities\Living\Player;

class FightEvent implements IEvent
{
    protected array $fightEvents;
    protected array $player = [];
    protected array $actors = [];
    protected array $colors;
    protected int $eventCounter = 0;

    public function __construct($fightEvents)
    {
        return $this->fightEvents = $fightEvents;
    }
    public function isFinished(): bool
    {
        if ($this->isAllMonstersDead()) {
            return true;
        }
        return false;
    }
    protected function isAllMonstersDead(): bool
    {
        $isAllDead = true;

        foreach ($this->getActors() as $monster) {
            if (!$monster->isDead()) {
                $isAllDead = false;
            }
        }
        return $isAllDead;
    }

    public function calculate(Player $player): void
    {
        $everyTwoSeconds = ((int)($this->eventCounter / 100) % 2) == 1;

        if ($this->eventCounter === 0 || $everyTwoSeconds) {
            $nameFontSize = 20;
            $fightEventActors = $this->getActors();
            $delta = 0;

            $playerCirclePositionX = GetScreenWidth() / 2;
            $playerCirclePositionY = GetScreenHeight() / 2 + 60;
            $this->player = [$player, $playerCirclePositionX, $playerCirclePositionY, $nameFontSize];

            /** @var ICreature $fightEventActor */
            foreach ($fightEventActors as $fightEventActor) {
                $creatureNameDeltaPosition = $this->getCreatureNameDeltaPosition($nameFontSize);
                $monsterCirclePositionX = GetScreenWidth() / 2 - $creatureNameDeltaPosition->getMonsterNameDeltaPosition() + $delta;
                $monsterCirclePositionY = GetScreenHeight() / 2 - 50;
                $this->actors[] = [$fightEventActor, $monsterCirclePositionX, $monsterCirclePositionY, $nameFontSize];

                $delta = $delta + $creatureNameDeltaPosition->getMonsterNameAndIndent();
            }


            if ($everyTwoSeconds) {
                $this->fight($player);
                $this->eventCounter = 0;
            }
        }
    }

    public function drawActor(ICreature $creature, int $circlePosX, int $circlePosY, int $nameFontSize): void
    {
        $creatureName = $creature->getName();
        $playerNamePositionX = $this->calculateCreatureNamePosition($creatureName, $nameFontSize);

        $color = $creature->getFightColor();
        $indent = 15;

        DrawCircle($circlePosX, $circlePosY, 10, $this->getColor($color));
        DrawText(
            $creature->getName(),
            $circlePosX - $playerNamePositionX,
            $circlePosY + $indent,
            20,
           $this->getColor('BLACK')
        );
        DrawRectangleLines(
            $circlePosX - $playerNamePositionX,
            $circlePosY + $indent + 25,
            MeasureText($creature->getName(), $nameFontSize),
            15,
            $this->getColor('DARKGREEN')
        );
        DrawRectangleGradientH(
            $circlePosX - $playerNamePositionX,
            $circlePosY + $indent + 25,
            (int) ((MeasureText($creature->getName(), $nameFontSize) / 100) * $creature->getCreatureHitPointsPercentage()),
            15,
            $this->getColor('DARKGREEN'),
            $this->getColor('GREEN')
        );
    }

    protected function getColor(string $color)
    {
        return $this->colors[$color];
    }

    public function draw(array $colors): void
    {
        $this->colors = $colors;

        $this->drawActor(...$this->player);

        foreach ($this->actors as $actor) {
            $this->drawActor(...$actor);
        }
    }

    public function isCounterNeeded(): bool
    {
        return true;
    }

    public function incrementCounter(int $qty): void
    {
        $this->eventCounter += $qty;
    }

    protected function getCreatureNameDeltaPosition($fontSize): CreatureNameDeltaPosition
    {
        $monsterNames = $this->getActorsNames();
        $longestMonsterName = max($monsterNames);
        $monsterNameLength = MeasureText($longestMonsterName, $fontSize);
        $monsterNameAndIndent = $monsterNameLength + 10;
        $monsterNameDeltaPosition = ((count($monsterNames) * $monsterNameAndIndent) / 2) - $monsterNameAndIndent / 2;

        return new CreatureNameDeltaPosition($monsterNameAndIndent, $monsterNameDeltaPosition);
    }

    protected function calculateCreatureNamePosition(string $name, int $fontSize): int
    {
        return MeasureText($name, $fontSize) / 2;
    }

    public function fight(Player $player): void
    {
        foreach ($this->fightEvents as $fightEvent) {
            foreach ($fightEvent as $monster) {
                if (!$player->isDead() && !$monster->isDead()) {
                    $player->fight($monster);
                    return;
                }
            }
        }
    }

    protected function getActors(): array
    {
        $actors = [];

        foreach ($this->fightEvents as $fightEvent) {
            foreach ($fightEvent as $monster) {
                $actors[] = $monster;
            }
        }

        return $actors;
    }

    protected function getActorsNames(): array
    {
        $monsterNames = [];

        foreach ($this->getActors() as $monster) {
            $monsterNames[] = $monster->getName();
        }

        return $monsterNames;
    }
    protected function countActors(): int
    {
        return count($this->getActors());
    }
    protected function getMonsterName(): string
    {
        foreach ($this->getActors() as $monster) {
            return $monster->getName();
        }
    }
    public function announceMessage(): string
    {
        return $this->countActors() . ' ' . $this->getMonsterName() . ' ' . 'attack you!';
    }
    public function confirmationMessage(): string
    {
        return 'Do you want to fight?';
    }
}