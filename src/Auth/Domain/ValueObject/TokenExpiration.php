<?php

namespace App\Auth\Domain\ValueObject;
use DateTimeImmutable;

final class TokenExpiration {
    private readonly DateTimeImmutable $expiration;

    private function __construct(DateTimeImmutable $value){
        $this->expiration = $value;
    }

    public static function generate() : self {
        $now = new DateTimeImmutable();
        $expiration = $now->modify('+30 minutes');
        return new self($expiration);
    }

    public function isExpired() : bool {
        $now = new DateTimeImmutable();
        return $now>$this->value();
    }

    public function value() : DateTimeImmutable {
        return $this->expiration;
    }
}