<?php

namespace App\Shared\Infrastructure\Di;
use ReflectionClass;

final class Container {
    private array $binds = [];
    private array $instances = [];

    public function __construct(){}

    public function get(string $requiredClass): object {
        if(!$requiredClass) throw new \InvalidArgumentException("La clase no puede estar vacia");

        $resolvedParams = [];
        $targetClass = $requiredClass;
        $implementation = null; 
        
        if(array_key_exists($requiredClass,$this->binds)){
            $implementation = $this->binds[$requiredClass];
        }

        if(!$implementation) throw new \InvalidArgumentException("La clase solicitada no existe en el container");

        if(array_key_exists($targetClass,$this->instances)){
            return $this->instances[$targetClass];
        }

        if(is_callable($implementation)){
            $this->instances[$targetClass] = $implementation($this);
        }else{
            $reflection = new ReflectionClass($implementation);
            $constructor = $reflection->getConstructor();
            if($constructor){
                $params = $constructor->getParameters();
                if($params){
                    foreach($params as $value){
                        $paramType = $value->getType();
                        $typeName = (string)$paramType;
                        $primitives = ['string', 'int', 'bool', 'float', 'array', 'object'];
                        if (!in_array($typeName, $primitives)) {
                            $resolvedParams[] = $this->get($typeName);
                        }
                    }
                    $this->instances[$targetClass] = $reflection->newInstanceArgs($resolvedParams);
                } else {
                    $this->instances[$targetClass] = $reflection->newInstance();
                }
            }else{
                $this->instances[$targetClass] = $reflection->newInstanceWithoutConstructor();
            }
        }
        return $this->instances[$targetClass];
    }

    public function bind(string $interface, string | callable $implementation){
        $this->binds[$interface] = $implementation;
    }
}