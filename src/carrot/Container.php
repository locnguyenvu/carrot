<?php
namespace Carrot;

class Container {
    public static $di;

    public function __construct()
    {
        static::$di = new \Carrot\DI();
    }

    
}
