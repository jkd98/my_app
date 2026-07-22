<?php

namespace App\Shared\Application\Port;

interface CookieManagerInterface {
    public function set(string $key,string $value, array $options):void;
    public function delete(string $key):void;
}