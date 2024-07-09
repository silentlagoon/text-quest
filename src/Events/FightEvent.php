<?php

namespace App\Events;

use App\Contracts\Entities\ICreature;
use App\Contracts\Events\IEvent;
use App\DTO\CreatureNameDeltaPosition;
use App\Entities\Living\Player;
use raylib\Color;

class FightEvent implements IEvent
{
    protected array $fightEvents;

    public function __construct($fightEvents)
    {
        return $this->fightEvents = $fightEvents;
    }

    public function handle(Player $player, int $framesCounter): void
    {
        $nameFontSize = 20;
        $fightEventActors = $this->getActors();
        $delta = 0;

        $playerCirclePositionX = GetScreenWidth() / 2;
        $playerCirclePositionY = GetScreenHeight() / 2 + 60;
        $this->drawActor($player, $playerCirclePositionX, $playerCirclePositionY, $nameFontSize);

        foreach ($fightEventActors as $fightEventActor) {
            $creatureNameDeltaPosition = $this->getCreatureNameDeltaPosition($nameFontSize);
            $monsterCirclePositionX = GetScreenWidth() / 2 - $creatureNameDeltaPosition->getMonsterNameDeltaPosition() + $delta;
            $monsterCirclePositionY = GetScreenHeight() / 2 - 50;
            $this->drawActor($fightEventActor, $monsterCirclePositionX, $monsterCirclePositionY, $nameFontSize);

            $delta = $delta + $creatureNameDeltaPosition->getMonsterNameAndIndent();
        }

        $this->initiateFightEvent($player, $framesCounter);
    }

    public function drawActor(ICreature $creature, int $circlePosX, int $circlePosY, int $nameFontSize): void
    {
        $creatureName = $creature->getName();
        $playerNamePositionX = $this->calculateCreatureNamePosition($creatureName, $nameFontSize);

        $color = $creature->getFightColor();
        $indent = 15;

        DrawCircle($circlePosX, $circlePosY, 10, Color::$color());
        DrawText(
            $creature->getName(),
            $circlePosX - $playerNamePositionX,
            $circlePosY + $indent,
            20,
            Color::BLACK()
        );
        DrawRectangleLines(
            $circlePosX - $playerNamePositionX,
            $circlePosY + $indent + 25,
            MeasureText($creature->getName(), $nameFontSize),
            15,
            Color::DARKGREEN()
        );
        DrawRectangleGradientH(
            $circlePosX - $playerNamePositionX,
            $circlePosY + $indent + 25,
            (MeasureText($creature->getName(), $nameFontSize) / 100) * $creature->getCreatureHitPointsPercentage(),
            15,
            Color::DARKGREEN(),
            Color::GREEN()
        );
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

    protected function initiateFightEvent(Player $player, int $framesCounter): void
    {
        foreach ($this->fightEvents as $fightEvent) {
            foreach ($fightEvent as $monster) {
                $player->fight($monster, $framesCounter);
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
}