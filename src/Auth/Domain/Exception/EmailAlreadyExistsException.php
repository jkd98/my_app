<?php
namespace App\Auth\Domain\Exception;
use Exception;

final class EmailAlreadyExistsException extends Exception {
    public function __construct(
        string $message = "El email ya esta registrado"
    ){
        parent::__construct($message);
    }
}