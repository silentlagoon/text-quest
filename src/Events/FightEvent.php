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

    public function handle(Player $player): void
    {
        $namesFontSize = 20;
        $fightEventActors = $this->getActors();
        $delta = 0;

        $playerCirclePositionX = GetScreenWidth() / 2;
        $playerCirclePositionY = GetScreenHeight() / 2 + 60;
        $this->drawActor($player, $playerCirclePositionX, $playerCirclePositionY, $namesFontSize);

        foreach ($fightEventActors as $fightEventActor) {
            $creatureNameDeltaPosition = $this->getCreatureNameDeltaPosition($namesFontSize);
            $monsterCirclePositionX = GetScreenWidth() / 2 - $creatureNameDeltaPosition->getMonsterNameDeltaPosition() + $delta;
            $monsterCirclePositionY = GetScreenHeight() / 2 - 50;
            $this->drawActor($fightEventActor, $monsterCirclePositionX, $monsterCirclePositionY, $namesFontSize);

            $delta = $delta + $creatureNameDeltaPosition->getMonsterNameAndIndent();
        }

        $this->initiateFightEvent($player);
    }

    public function drawActor(ICreature $creature, $circlePosX, $circlePosY, $nameFontSize)
    {
        $creatureName = $creature->getName();
        $playerNamePositionX = $this->calculateCreatureNamePosition($creatureName, $nameFontSize);

        $color = $creature->getFightColor();

        DrawCircle($circlePosX, $circlePosY, 10, Color::$color());
        DrawText(
            $creature->getName(),
            $circlePosX - $playerNamePositionX,
            $circlePosY + 15,
            20,
            Color::BLACK()
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

    protected function initiateFightEvent(Player $player): void
    {
        foreach ($this->fightEvents as $fightEvent) {
            foreach ($fightEvent as $monster) {
                $player->fight($monster);
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