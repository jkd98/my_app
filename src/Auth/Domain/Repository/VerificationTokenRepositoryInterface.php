<?php

namespace App\Auth\Domain\Repository;
use App\Auth\Domain\Entity\VerificationToken;
use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\ValueObject\TokenId;

interface VerificationTokenRepositoryInterface {
    public function save(VerificationToken $data) : void;
    public function findByTokenValue(TokenValue $value): ?VerificationToken;
    public function delete(TokenId $id):void;
}