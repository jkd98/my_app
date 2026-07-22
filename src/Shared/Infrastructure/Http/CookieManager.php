<?php

namespace App\Shared\Infrastructure\Http;
use App\Shared\Application\Port\CookieManagerInterface;

final class CookieManager implements CookieManagerInterface {
    public function set(string $key,string $value, array $options):void{
        setcookie($key,$value,$options);
    }

    public function delete(string $key):void{
        unset($_COOKIE[$key]);
    }
}