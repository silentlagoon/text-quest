<?php

namespace App;

use App\Entities\Player;
use Illuminate\Support\Collection;
use raylib\Color;
use raylib\Rectangle;
use const raylib\MouseButton\MOUSE_BUTTON_LEFT;

class Game
{
    const SCREEN_WIDTH = 800;
    const SCREEN_HEIGHT = 450;

    protected Collection $playersAvailable;

    private bool $isPlayerSelected = false;
    private Player $currentPlayer;

    public function __construct(Collection $playersAvailable)
    {
        $this->playersAvailable = $playersAvailable;
    }

    public function start()
    {
        $lightGray    = new Color(245, 245, 245, 255);
        $gray         = new Color(200, 200, 200, 255);
        $black         = new Color(200, 200, 200, 255);
        $tansp = new Color(0, 0, 0, 0);

        InitWindow(static::SCREEN_WIDTH, static::SCREEN_HEIGHT, "raylib [core] example - basic window");

        //$playerRectangle = new Rectangle(GetScreenWidth() / 2, GetScreenHeight() / 2, 60, 20);

        SetTargetFPS(60);

        while (!WindowShouldClose())
        {
            // Update
            //----------------------------------------------------------------------------------
            // TODO: Update your variables here
            //----------------------------------------------------------------------------------

            // Draw
            //----------------------------------------------------------------------------------

            BeginDrawing();

                ClearBackground($lightGray);

                if (!$this->isPlayerSelected()) {
                    $this->playerSelectScene();
                }

            EndDrawing();
            //----------------------------------------------------------------------------------
        }

        CloseWindow();
    }

    /**
     * @return bool
     */
    public function isPlayerSelected(): bool
    {
        return $this->isPlayerSelected;
    }

    /**
     * @param bool $isPlayerSelected
     */
    public function setIsPlayerSelected(bool $isPlayerSelected): void
    {
        $this->isPlayerSelected = $isPlayerSelected;
    }

    protected function playerSelectScene()
    {
        $tansp = new Color(0, 0, 0, 0);

        $playerNamePositionDelta = 30;
        /** @var Player $player */
        foreach ($this->playersAvailable as $player) {
            $playerRectangle = new Rectangle(
                GetScreenWidth() / 2,
                (GetScreenHeight() / 2) - $playerNamePositionDelta,
                MeasureText($player->getName(), 20),
                20
            );

            $collision = CheckCollisionPointRec(GetMousePosition(), $playerRectangle);

            DrawText(
                $player->getName(),
                GetScreenWidth() / 2,
                (GetScreenHeight() / 2) - $playerNamePositionDelta,
                20,
                $collision ? Color::SKYBLUE() : Color::BLACK()
            );

            DrawRectangleRec($playerRectangle, $tansp);

            $playerNamePositionDelta += 30;

            if ($collision) {
                if(IsMouseButtonPressed(MOUSE_BUTTON_LEFT)) {
                    $this->isPlayerSelected = true;
                    $this->currentPlayer = $player;
                }
            }
        }
    }
}