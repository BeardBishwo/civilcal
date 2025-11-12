<?php
namespace App\Modules;

abstract class BaseProvider
{
    abstract public function register($router): void;
}
