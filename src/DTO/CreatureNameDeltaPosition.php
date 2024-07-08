<?php

namespace App\DTO;

class CreatureNameDeltaPosition
{
    public int $monsterNameAndIndent;
    public int $monsterNameDeltaPosition;

    public function __construct(int $monsterNameAndIndent, int $monsterNameDeltaPosition)
    {
        $this->monsterNameAndIndent = $monsterNameAndIndent;
        $this->monsterNameDeltaPosition = $monsterNameDeltaPosition;
    }

    public function getMonsterNameAndIndent(): int
    {
        return $this->monsterNameAndIndent;
    }

    public function getMonsterNameDeltaPosition(): int
    {
        return $this->monsterNameDeltaPosition;
    }
}