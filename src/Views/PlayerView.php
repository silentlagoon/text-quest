<?php

namespace App\Views;

use App\Entities\Living\Player;
use raylib\Color;
use raylib\Rectangle;

class PlayerView
{
    protected Rectangle $rectangle;
    protected Player $player;
    protected Color $color;
    protected int $namePositionDelta;

    /**
     * @return Rectangle
     */
    public function getRectangle(): Rectangle
    {
        return $this->rectangle;
    }

    /**
     * @param Rectangle $rectangle
     * @return PlayerView
     */
    public function setRectangle(Rectangle $rectangle): PlayerView
    {
        $this->rectangle = $rectangle;

        return $this;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @param Player $player
     * @return PlayerView
     */
    public function setPlayer(Player $player): PlayerView
    {
        $this->player = $player;

        return $this;
    }

    /**
     * @return int
     */
    public function getNamePositionDelta(): int
    {
        return $this->namePositionDelta;
    }

    /**
     * @param int $namePositionDelta
     * @return PlayerView
     */
    public function setNamePositionDelta(int $namePositionDelta): PlayerView
    {
        $this->namePositionDelta = $namePositionDelta;

        return $this;
    }

    /**
     * @return Color
     */
    public function getColor(): Color
    {
        return $this->color;
    }

    /**
     * @param Color $color
     * @return PlayerView
     */
    public function setColor(Color $color): PlayerView
    {
        $this->color = $color;

        return $this;
    }
}
