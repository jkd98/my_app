<?php

namespace App\Auth\Domain\ValueObject;
use App\Shared\Domain\ValueObject\UUIDv7;

final class UserId {
    private readonly UUIDv7 $uuidv7;

    private function __construct(UUIDv7 $uuidv7) {
        $this->uuidv7 = $uuidv7;
    }
        
    public static function generate() : self {
        $uuidv7 = UUIDv7::generate();
        return new self($uuidv7);
    }

    public static function fromString(string $value) : self {
        $uuidv7 = UUIDv7::fromString($value);
        return new self($uuidv7);
    }

    public function equals(UserId $other_uuid) : bool {
        return $this->value() === $other_uuid->value();
    }

    public function value() : string {
        return $this->uuidv7->value();
    }
}