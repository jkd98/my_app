<?php
namespace App\Auth\Domain\Exception;


final class UserNotFoundException extends \Exception{
    public function __construct(string $msg = "Usuario no encontrado"){
        parent::__construct($msg);
    }
}