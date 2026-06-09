<?php

namespace App\Auth\Domain\ValueObject;
use InvalidArgumentException;

final class Password {
    private readonly string $passwordHashed;

    private function __construct(string $hash){
        $this->passwordHashed = $hash;
    }

    public static function create(string $hash) : self {
        return strlen($hash) > 0 ? new self($hash) : throw new InvalidArgumentException("El valor es obligatorio");
    }

    public function value() : string {
        return $this->passwordHashed;
    }
}