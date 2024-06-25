<?php

require '../vendor/autoload.php';

use App\Entities\Player;
use App\Game;

$playersAvailable = collect([
    new Player('boganella', 100, 20),
    new Player('silentlagoon', 100, 20),
    new Player('ilya', 80, 10),
]);

$game = new Game($playersAvailable);
$game->start();
