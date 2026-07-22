<?php

namespace App\Shared\Infrastructure\Http;
use App\Shared\Application\Port\CookieManagerInterface;

final class CookieManager implements CookieManagerInterface {
    public function set(string $key,string $value, array $options):void{
        setcookie($key,$value,$options);
    }

    public function delete(string $key):void{
        setcookie($key,"",[
            "expires" => time() - (50*24*60*60),
            "httpOnly" => true,
            "secure" => true,
            "sameSite" => "Lax"
        ]);
    }
}