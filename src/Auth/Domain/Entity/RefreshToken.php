<?php

namespace App\Auth\Domain\Entity;
use App\Auth\Domain\ValueObject\TokenId;
use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\ValueObject\RefreshTokenExpiration;
use App\Auth\Domain\ValueObject\UserId;

final class RefreshToken {
    private function __construct(
        private readonly TokenId $tokenId,
        private readonly TokenValue $tokenValue,
        private readonly RefreshTokenExpiration $refreshTokenExpiration,
        private readonly UserId $userId,
        private readonly string $userAgent
    ){}

    public static function generate(UserId $userId,string $userAgent) : self {
        $tokenId = TokenId::generate();
        $tokenValue = TokenValue::generate();
        $refreshTokenExpiration = RefreshTokenExpiration::generate();
        return new self(
            $tokenId,
            $tokenValue,
            $refreshTokenExpiration,
            $userId,
            $userAgent
        );
    }

    public static function reconstitute(
        TokenId $tokenId,
        TokenValue $tokenValue,
        RefreshTokenExpiration $refreshTokenExpiration,
        UserId $userId,
        string $userAgent
    ):self {
        return new self(
            $tokenId,
            $tokenValue,
            $refreshTokenExpiration,
            $userId,
            $userAgent
        );
    }

    public function isExpired() : bool {
        return $this->refreshTokenExpiration->isExpired();
    }

    public function tokenId(): TokenId { return $this->tokenId; }
    public function tokenValue(): TokenValue { return $this->tokenValue; }
    public function refreshTokenExpiration(): RefreshTokenExpiration { return $this->refreshTokenExpiration; }
    public function userId(): UserId { return $this->userId; }
    public function userAgent(): string { return $this->userAgent; }
}