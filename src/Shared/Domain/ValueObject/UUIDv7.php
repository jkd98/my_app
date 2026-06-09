<?php
namespace App\Shared\Domain\ValueObject;

use Ramsey\Uuid\Uuid;
use InvalidArgumentException;

final class UUIDv7 {
    private readonly string $value;

    private function __construct(string $value){
        $this->value = $value;
    }

    public static function generate() : self {
        $uuid = Uuid::uuid7();
        return new self($uuid->toString());
    }

    public static function fromString(string $value) : self  {
        return Uuid::isValid($value) ? new self($value) : throw new InvalidArgumentException("El valor no es un UUID válido");
    }

    public function value(): string {
        return $this->value;
    }

    public function equals(UUIDv7 $uuid): bool {
        return $this->value === $uuid->value;
    }
}
