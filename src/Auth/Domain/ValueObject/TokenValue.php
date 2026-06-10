<?php

namespace App\Auth\Domain\ValueObject;

final class TokenValue {
    private readonly string $value;

    private function __construct(string $chars){
        $this->value = $chars;
    }

    public static function generate() : self {
        $randomBytes = bin2hex(random_bytes(32));
        return new self($randomBytes);
    }
    
    public static function fromString(string $str_value) : self {
        return new self($str_value);
    }

    public function value() : string {
        return $this->value;
    }

}