<?php

namespace App\Contracts\Views\UI\StatusBar;

use raylib\Color;

interface IStatusBarElement
{
    public function draw(?Color $trueColor = null, ?Color $falseColor = null): void;
}