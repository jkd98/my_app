<?php

namespace App\Shared\Infrastructure\Di;
use App\Shared\Infrastructure\Di\Container;


final class ContainerConfig {
    private function __construct() {}

    public static function create(){
        $container = new Container();
        
    }

}