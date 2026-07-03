<?php

namespace App\Shared\Infrastructure\Bootstrap;

final class EnvironmentLoader {
    private function __construct(){}
    public static function load(){
        $path = __DIR__."/../../../../.env";
        $content = file_get_contents($path);
        $arrayContent = explode("\n",$content);
        foreach($arrayContent as $value){
            //echo $value;
            if( preg_match('/^#/',$value) || $value==="" ) continue;
            putenv($value);
        }
    }
}