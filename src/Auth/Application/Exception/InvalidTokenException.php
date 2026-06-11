<?php

namespace App\Auth\Application\Exception;
use Exception;

final class InvalidTokenException extends Exception {
    public function __construct(string $msg="El token no es válido"){
        parent::__construct($msg);
    }
}