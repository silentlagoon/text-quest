<?php

namespace App\Views\UI\StatusBar;

use App\Contracts\Views\UI\StatusBar\IStatusBarElement;
use App\Views\AbstractView;
use raylib\Color;

abstract class BaseStatusBarView extends AbstractView implements IStatusBarElement
{
    protected string $name;
    protected int $posX;
    protected int $posY;
    protected int $fontSize;
    protected Color $color;

    public function draw(): void
    {
        DrawText(
            $this->name,
            $this->posX,
            $this->posY,
            $this->fontSize,
            $this->color
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return BaseStatusBarView
     */
    public function setName(string $name): BaseStatusBarView
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosX(): int
    {
        return $this->posX;
    }

    /**
     * @param int $posX
     * @return BaseStatusBarView
     */
    public function setPosX(int $posX): BaseStatusBarView
    {
        $this->posX = $posX;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosY(): int
    {
        return $this->posY;
    }

    /**
     * @param int $posY
     * @return BaseStatusBarView
     */
    public function setPosY(int $posY): BaseStatusBarView
    {
        $this->posY = $posY;

        return $this;
    }

    /**
     * @return int
     */
    public function getFontSize(): int
    {
        return $this->fontSize;
    }

    /**
     * @param int $fontSize
     * @return BaseStatusBarView
     */
    public function setFontSize(int $fontSize): BaseStatusBarView
    {
        $this->fontSize = $fontSize;

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
     * @return BaseStatusBarView
     */
    public function setColor(Color $color): BaseStatusBarView
    {
        $this->color = $color;

        return $this;
    }
}