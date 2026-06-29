<?php

namespace App\Shared\Infrastructure\Di;

final class Container {
    private array $binds = [];
    private array $instances = [];

    public function __construct(){}

    public function get(string $requiredClass){
        $targetClass = $requiredClass;
        
        if(array_key_exists($requiredClass,$this->binds)){
            $targetClass = $this->$binds[$requiredClass];
        }

        if(array_key_exists($targetClass,$this->instances)){
            return $this->instances[$targetClass];
        }
    }

    public function bind(string $interface, string $implementation){
        $updateBinds = [ ...$this->binds, $interface => $implementation ];
        $this->$binds = $updateBinds;
    }
}