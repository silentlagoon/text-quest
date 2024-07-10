<?php

namespace App\Views;

use raylib\Color;

abstract class AbstractView
{
    abstract public function draw(?Color $trueColor = null, ?Color $falseColor = null): void;
}