<?php

namespace App\Auth\Domain\ValueObject;
use InvalidArgumentException;

final class RawPassword {
    private readonly string $rawPass;

    private function __construct(string $rawPass){
        $this->rawPass = $rawPass;
    }

    public static function create(string $passTxt) : self {
        return strlen($passTxt) < 8 ? throw new InvalidArgumentException("La longitud de la contraseña debe ser mayor o igual a 8 caracteres") : new self($passTxt);
    }

    public function value() : string {
        return $this->rawPass;
    }
}