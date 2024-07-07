<?php

namespace App;

use App\Entities\Living\Player;
use App\Entities\Quest\Room;
use App\Events\FightEvent;
use App\Views\PlayerView;
use Illuminate\Support\Collection;
use raylib\Color;
use raylib\Font;
use raylib\Rectangle;
use Relay\Event;
use const raylib\MouseButton\MOUSE_BUTTON_LEFT;

class Game
{
    const SCREEN_WIDTH = 800;
    const SCREEN_HEIGHT = 450;
    const START_ROOM_ID = 'start_room';

    protected Collection $playersAvailable;
    protected ?Player $currentPlayer;
    protected Collection $rooms;
    protected Room $currentRoom;

    public function __construct(Collection $playersAvailable)
    {
        $this->currentPlayer = null;
        $this->playersAvailable = $playersAvailable;
        $this->rooms = $this->initRooms();

        $this->currentRoom = $this->findRoomById(static::START_ROOM_ID);
    }

    public function start()
    {
        $lightGray    = new Color(245, 245, 245, 255);

        InitWindow(static::SCREEN_WIDTH, static::SCREEN_HEIGHT, "Dungeon explorers");

        SetTargetFPS(60);

        while (!WindowShouldClose())
        {
            //Updating variables Before BeginDrawing()
            $selectionScreenPlayers = $this->getSelectionScreenPlayers();

            BeginDrawing();

                ClearBackground($lightGray);

                if (!$this->isCurrentPlayerSelected()) {
                    $this->drawPlayerSelectScene($selectionScreenPlayers);
                }

                if ($this->isCurrentPlayerSelected()) {
                    $this->drawMainScreenScene();
                }

            EndDrawing();
        }

        CloseWindow();
    }

    protected function isCurrentPlayerSelected(): bool
    {
        return !is_null($this->currentPlayer);
    }

    protected function setCurrentPlayer(Player $player): void
    {
        $this->currentPlayer = $player;
    }

    protected function getCurrentPlayer(): ?Player
    {
        return $this->isCurrentPlayerSelected() ? $this->currentPlayer : null;
    }

    protected function drawPlayerSelectScene(array $selectionScreenPlayers): void
    {
        /** @var PlayerView $playerView */
        foreach ($selectionScreenPlayers as $playerView) {

            $collision = CheckCollisionPointRec(GetMousePosition(), $playerView->getRectangle());

            DrawText(
                $playerView->getPlayer()->getName(),
                GetScreenWidth() / 2,
                (GetScreenHeight() / 2) - $playerView->getNamePositionDelta(),
                20,
                $collision ? Color::SKYBLUE() : Color::BLACK()
            );

            if ($collision) {
                if(IsMouseButtonPressed(MOUSE_BUTTON_LEFT)) {
                    $this->setCurrentPlayer($playerView->getPlayer());
                }
            }

        }
    }

    protected function drawPlayerStatusBar(): void
    {
        $textFontSize = 20;
        $textStartPositionX = 10;

        $playerNameString = $this->getCurrentPlayer()->getName() . ': ';
        DrawText(
            $playerNameString,
            $textStartPositionX,
            0,
            $textFontSize,
            Color::SKYBLUE()
        );

        $playerHitPointsString = 'HP ' . $this->getCurrentPlayer()->getHitPoints() . ' ';
        DrawText(
            $playerHitPointsString,
            MeasureText($playerNameString, $textFontSize) + $textStartPositionX,
            0,
            $textFontSize,
            Color::GREEN()
        );

        DrawText(
            'DMG ' . $this->getCurrentPlayer()->getDamage(),
            MeasureText($playerNameString . $playerHitPointsString, $textFontSize) + $textStartPositionX,
            0,
            $textFontSize,
            Color::MAROON()
        );
    }

    protected function drawCurrentRoom()
    {
        $textRoomNamePositionX =  intval(GetScreenWidth() / 6 - 100);
        $textRoomNamePositionY =  intval(GetScreenHeight() / 6);
        $roomExitsTextDeltaY = 32;
        $fontSize = 32;

        $currentRoomName = $this->currentRoom->getName() . ':';

        DrawText(
            $currentRoomName,
            $textRoomNamePositionX,
            $textRoomNamePositionY,
            $fontSize,
            Color::BLACK()
        );

        DrawLine(
            $textRoomNamePositionX,
            $textRoomNamePositionY + $fontSize,
            MeasureText($currentRoomName, $fontSize) + $textRoomNamePositionX + 5,
            $textRoomNamePositionY + $fontSize,
            Color::DARKGRAY()
        );

        DrawText(
            $this->currentRoom->getDescription(),
            $textRoomNamePositionX,
            $textRoomNamePositionY + $fontSize + 10,
            $fontSize,
            Color::BLACK()
        );

        foreach ($this->currentRoom->getEvents() as $event) {
            if ($event instanceof FightEvent) {
                $event->initiateFightEvent($this->getCurrentPlayer());
            }
        }

        $exits = $this->currentRoom->getExits();

        $roomExitDeltaY = 0;

        foreach ($exits as $exit) {
            $roomExit = $this->findRoomById($exit);

            $exitRoomNameText = '* ' . $roomExit->getName();

            $exitRoomRectangle = new Rectangle(
                $textRoomNamePositionX,
                GetScreenHeight() / 2 + $roomExitDeltaY,
                MeasureText($exitRoomNameText, $fontSize),
                $fontSize
            );

            $collision = CheckCollisionPointRec(GetMousePosition(), $exitRoomRectangle);

            DrawText(
                $exitRoomNameText,
                $textRoomNamePositionX,
                GetScreenHeight() / 2 + $roomExitDeltaY,
                $fontSize,
                $collision ? Color::SKYBLUE() : Color::DARKGREEN()
            );

            if ($collision) {
                if(IsMouseButtonPressed(MOUSE_BUTTON_LEFT)) {
                    dump($roomExit);
                    $this->setCurrentRoom($roomExit);
                }
            }

            $roomExitDeltaY += $roomExitsTextDeltaY;
        }
    }


    protected function drawMainScreenScene(): void
    {
        $this->drawPlayerStatusBar();

        $this->drawCurrentRoom();
    }

    protected function getSelectionScreenPlayers(): array
    {
        $selectionScreenPlayers = [];

        $playerNamePositionDelta = 30;

        foreach ($this->playersAvailable as $player) {
            $playerRectangle = new Rectangle(
                GetScreenWidth() / 2,
                (GetScreenHeight() / 2) - $playerNamePositionDelta,
                MeasureText($player->getName(), 20),
                20
            );

            $selectionScreenPlayers[] = (new PlayerView())
                ->setPlayer($player)
                ->setRectangle($playerRectangle)
                ->setColor(new Color(0, 0, 0, 0))
                ->setNamePositionDelta($playerNamePositionDelta);

            $playerNamePositionDelta += 30;
        }

        return $selectionScreenPlayers;
    }

    protected function initRooms(): Collection
    {
        $roomsSet = require 'Config/room.php';
        $rooms = collect([]);

        foreach ($roomsSet as $room) {

            $roomObject = new Room(
                $room['id'],
                $room['name'],
                $room['description']
            );

            $roomObject
                ->setEvents(collect($room['events']))
                ->setExits(collect($room['exits']));

            $rooms->push($roomObject);
        }

        return $rooms;
    }

    protected function findRoomById(string $roomId): Room
    {
        return $this->rooms->where(function (Room $room) use ($roomId) {
            return $room->getId() === $roomId;
        })->first();
    }

    public function setCurrentRoom(Room $room): void
    {
        $this->currentRoom = $room;
    }
}