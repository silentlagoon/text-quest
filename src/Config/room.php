<?php

use App\Entities\Living\Monster;
use App\Events\FightEvent;

return [
    [
        'id' => 'start_room',
        'name' => 'Dungeon entrance',
        'description' => 'You are at the beginning of a dungeon',
        'events' => [],
        'exits' => ['room_with_treasure_1', 'room_with_fight_1'],
    ],
    [
        'id' => 'room_with_treasure_1',
        'name' => 'Steel door entrance',
        'description' => 'Room seems to be some sort of abandoned warehouse',
        'events' => [
            new FightEvent([
                [
                    new Monster('kutya', 100,5),
                    new Monster('kutya', 100,5),
                ],
                [
                    new Monster('kutya', 100,5),
                    new Monster('kutya', 100,5),
                ]
            ])
        ],
        'exits' => ['start_room', 'room_with_fight_2'],
    ],
    [
        'id' => 'room_with_fight_1',
        'name' => 'Wooden door entrance',
        'description' => 'You went right',
        'events' => [],
        'exits' => ['start_room'],
    ],
    [
        'id' => 'room_with_fight_2',
        'name' => 'A musty corridor covered in cobwebs',
        'description' => 'A fight begins!',
        'events' => [],
        'exits' => ['room_with_treasure_1'],
    ]
];