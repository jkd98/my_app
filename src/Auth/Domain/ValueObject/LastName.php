<?php

namespace App\Auth\Domain\ValueObject;
use InvalidArgumentException;

final class LastName {
    private readonly string $value;

    private function __construct(string $lastName) {
        $this->value = $lastName;
    }

    public static function create(string $txt) : self {
        if(!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/',$txt)){
            throw new InvalidArgumentException("Solo se admiten letras");
        } else {
            return strlen($txt) < 3 || strlen($txt) > 32 ? throw new InvalidArgumentException("Se debe tener al menos 3 letras y un máximo de 32") : new self($txt);
        }
    }

    public function value() : string{
        return $this->value;
    }
}