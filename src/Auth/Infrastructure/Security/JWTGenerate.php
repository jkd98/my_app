<?php

namespace App\Auth\Infrastructure\Security;

use App\Auth\Application\Security\TokenGeneratorInterface;
use Firebase\JWT\JWT;
use App\Auth\Domain\ValueObject\UserId;
use DateTimeImmutable;

final class JWTGenerate implements TokenGeneratorInterface {
    public function __construct(
        private readonly string $privateKey,
    ){}

    public function generate(UserId $userId):string {
        $now = new DateTimeImmutable();
        $exp = $now->modify("+15 minutes");
        $payload = [
            "sub"=>$userId->value(),
            "exp"=>$exp->getTimestamp()
        ];
        $token = JWT::encode($payload, $this->privateKey, 'RS256');
        return $token;
    }
}

