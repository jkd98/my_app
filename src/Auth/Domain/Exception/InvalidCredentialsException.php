<?php

namespace App\Auth\Domain\Exception;
use Exception;

final class InvalidCredentialsException extends Exception {
    public function __construct(string $msg="Credenciales incorrectas"){
        parent::__construct($msg);
    }
}