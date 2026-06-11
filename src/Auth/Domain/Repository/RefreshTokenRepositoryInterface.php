<?php

namespace App\Auth\Domain\Repository;
use App\Auth\Domain\Entity\RefreshToken;
use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\ValueObject\TokenId;
use App\Auth\Domain\ValueObject\UserId;

interface RefreshTokenRepositoryInterface {
    public function save(RefreshToken $refreshToken):void;
    public function findByTokenValue(TokenValue $tokenValue):?RefreshToken;
    public function delete(TokenId $tokenId):void;
    public function deleteAll(UserId $userId):void;
}