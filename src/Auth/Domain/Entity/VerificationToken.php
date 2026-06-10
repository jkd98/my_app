<?php

namespace App\Auth\Domain\Entity;

use App\Auth\Domain\ValueObject\TokenId;
use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\ValueObject\TokenExpiration;
use App\Auth\Domain\ValueObject\TokenType;
use App\Auth\Domain\ValueObject\UserId;

final class VerificationToken {
    private function __construct(
        private readonly TokenId $tokenId,
        private readonly TokenValue $tokenValue,
        private readonly TokenExpiration $tokenExpiration,
        private readonly TokenType $tokenType,
        private readonly UserId $userId,
    ){}

    public static function create(
        TokenType $tokenType,
        UserId $userId,
    ) : self {
        $tokenId = TokenId::generate();
        $tokenValue = TokenValue::generate();
        $tokenExpiration = TokenExpiration::generate();
        return new self(
            $tokenId,
            $tokenValue,
            $tokenExpiration,
            $tokenType,
            $userId,
        );
    }

    public static function reconstitute(
        TokenId $tokenId,
        TokenValue $tokenValue,
        TokenExpiration $tokenExpiration,
        TokenType $tokenType,
        UserId $userId,
    ) : self {
        return new self(
            $tokenId,
            $tokenValue,
            $tokenExpiration,
            $tokenType,
            $userId,
        );
    }

    public function isExpired() : bool {
        return $this->tokenExpiration->isExpired();
    }

    public function tokenId(): TokenId {return $this->tokenId; }
    public function tokenValue(): TokenValue { return  $this->tokenValue; }
    public function tokenExpiration(): TokenExpiration { return $this->tokenExpiration; }
    public function tokenType(): TokenType { return $this->tokenType; }
    public function userId(): UserId { return $this->userId; }


}