<?php

namespace App\Auth\Domain\Repository;
use App\Auth\Domain\Entity\VerificationToken;
use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\ValueObject\TokenId;
use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\ValueObject\TokenType;

interface VerificationTokenRepositoryInterface {
    public function save(VerificationToken $data) : void;
    public function findByTokenValue(TokenValue $value): ?VerificationToken;
    public function findByTokenTypeAndUserId(TokenType $tokenType, UserId $userId): ?VerificationToken;
    public function delete(TokenId $id):void;
    public function deleteAllByTokenType(UserId $userId,TokenType $tokenType):void;
}