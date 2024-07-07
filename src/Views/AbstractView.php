<?php

namespace App\Views;

abstract class AbstractView
{
    abstract public function draw(): void;
}