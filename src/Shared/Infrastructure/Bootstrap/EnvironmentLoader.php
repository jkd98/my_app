<?php

namespace App\Shared\Infrastructure\Bootstrap;

final class EnvironmentLoader {
    private function __construct(){}
    public static function load(){
        $content = file_get_contents('.env');
        $arrayContent = explode("\n",$content);
        foreach($arrayContent as $value){
            //echo $value;
            putenv($value);
        }
    }
}