<?php

namespace App;

use App\Contracts\Views\UI\StatusBar\IStatusBarElement;
use App\Entities\Living\Creature;
use App\Entities\Living\Monster;
use App\Entities\Living\Player;
use App\Entities\Quest\Room;
use App\Events\FightEvent;
use App\Views\PlayerView;
use App\Views\UI\StatusBar\DamageView;
use App\Views\UI\StatusBar\HitPointsView;
use App\Views\UI\StatusBar\NameView;
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
        $lightGray = new Color(245, 245, 245, 255);

        InitWindow(static::SCREEN_WIDTH, static::SCREEN_HEIGHT, "Dungeon explorers");

        SetTargetFPS(60);

        $uiElements = [
            new NameView(),
            new HitPointsView(),
            new DamageView()
        ];

        while (!WindowShouldClose())
        {
            //Updating variables Before BeginDrawing()
            $selectionScreenPlayers = $this->getSelectionScreenPlayers();
            if ($this->isCurrentPlayerSelected()) {
                $this->setUIElements($uiElements);
            }

            BeginDrawing();

                ClearBackground($lightGray);

                if (!$this->isCurrentPlayerSelected()) {
                    $this->drawPlayerSelectScene($selectionScreenPlayers);
                }

                if ($this->isCurrentPlayerSelected()) {
                    $this->drawMainScreenScene($uiElements);
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
            $playerView->draw();
        }
    }

    protected function drawPlayerStatusBar(array $uiElements): void
    {
        /** @var IStatusBarElement $element */
        foreach ($uiElements as $element) {
            $element->draw();
        }
    }

    protected function drawCurrentRoom()
    {
        $textRoomNamePositionX =  intval(GetScreenWidth() / 6 - 100);
        $textRoomNamePositionY =  intval(GetScreenHeight() / 6);
        $roomExitsTextDeltaY = 32;
        $fontSize = 32;
        $namesFontSize = 20;

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
                $fightEventActors = $event->getActors();
                $fightEventActors[] = $this->getCurrentPlayer();

                $monsterNames = [];
                foreach ($fightEventActors as $fightEventActor) {
                    if ($fightEventActor instanceof Monster) {
                        $monsterNames[] = $fightEventActor->getName();
                    }
                }

                $longestMonsterName = max($monsterNames);
                $monsterNameLenght = MeasureText($longestMonsterName, $namesFontSize);
                $monsterNameAndIndent = $monsterNameLenght + 10;
                $monsterNameDeltaPosition = ((count($monsterNames) * $monsterNameAndIndent) / 2) - $monsterNameAndIndent / 2;
                $delta = 0;

                foreach ($fightEventActors as $fightEventActor) {
                    if ($fightEventActor instanceof Player) {
                        $playerCirclePositionX = GetScreenWidth() / 2;
                        $playerCirclePositionY = GetScreenHeight() / 2 + 60;
                        $playerNamePositionX = MeasureText($this->getCurrentPlayer()->getName(), $namesFontSize) / 2;
                        DrawCircle($playerCirclePositionX, $playerCirclePositionY, 10, Color::BLUE());
                        DrawText(
                            $this->getCurrentPlayer()->getName(),
                            $playerCirclePositionX - $playerNamePositionX,
                            $playerCirclePositionY + 15,
                            20,
                            Color::BLACK()
                        );
                    }

                    if ($fightEventActor instanceof Monster) {
                        $monsterCirclePositionX = GetScreenWidth() / 2 - $monsterNameDeltaPosition + $delta;
                        $monsterCirclePositionY = GetScreenHeight() / 2 - 50;
                        $monsterNamePositionX = MeasureText($fightEventActor->getName(), $namesFontSize) / 2;
                        DrawCircle($monsterCirclePositionX, $monsterCirclePositionY, 10, Color::MAROON());
                        DrawText(
                            $fightEventActor->getName(),
                            $monsterCirclePositionX - $monsterNamePositionX,
                            $monsterCirclePositionY + 15,
                            20,
                            Color::BLACK()
                        );
                        $delta = $delta + $monsterNameAndIndent;
                    }
                }
                $event->initiateFightEvent($this->getCurrentPlayer());
            }
        }

        $exits = $this->currentRoom->getExits();

        $roomExitDeltaY = 100;

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

    protected function drawMainScreenScene(array $uiElements): void
    {
        $this->drawPlayerStatusBar($uiElements);

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

            $playerView = (new PlayerView())
                ->setPlayer($player)
                ->setRectangle($playerRectangle)
                ->setColor(new Color(0, 0, 0, 0))
                ->setNamePositionDelta($playerNamePositionDelta);

            $playerView->checkCollision();

            if ($playerView->getCollision()) {
                if(IsMouseButtonPressed(MOUSE_BUTTON_LEFT)) {
                    $this->setCurrentPlayer($player);
                }
            }

            $selectionScreenPlayers[] = $playerView;
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

    protected function setUIElements(array $uiElement): void
    {
        $textFontSize = 20;
        $textStartPositionX = 10;

        $playerNameString = $this->getCurrentPlayer()->getName() . ': ';
        $playerHitPointsString = 'HP ' . $this->getCurrentPlayer()->getHitPoints() . ' ';
        $playerDamageString = 'DMG ' . $this->getCurrentPlayer()->getDamage();

        $playerHitPointsPosX = MeasureText($playerNameString, $textFontSize) + $textStartPositionX;
        $playerDamagePosX = MeasureText($playerNameString . $playerHitPointsString, $textFontSize) + $textStartPositionX;

        foreach ($uiElement as $element) {
            if ($element instanceof NameView) {
                $element
                    ->setName($playerNameString)
                    ->setPosX($textStartPositionX)
                    ->setPosY(0)
                    ->setFontSize($textFontSize)
                    ->setColor(Color::SKYBLUE());
            }

            if ($element instanceof HitPointsView) {
                $element
                    ->setName($playerHitPointsString)
                    ->setPosX($playerHitPointsPosX)
                    ->setPosY(0)
                    ->setFontSize($textFontSize)
                    ->setColor(Color::GREEN());
            }

            if ($element instanceof DamageView) {
                $element
                    ->setName($playerDamageString)
                    ->setPosX($playerDamagePosX)
                    ->setPosY(0)
                    ->setFontSize($textFontSize)
                    ->setColor(Color::MAROON());
            }
        }
    }
}