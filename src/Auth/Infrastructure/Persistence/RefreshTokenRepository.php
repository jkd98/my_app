<?php

namespace App\Auth\Infrastructure\Persistence;

use App\Auth\Domain\Repository\RefreshTokenRepositoryInterface;
use App\Auth\Domain\Entity\RefreshToken;
use App\Auth\Domain\ValueObject\TokenId;
use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\ValueObject\RefreshTokenExpiration;
use App\Auth\Domain\ValueObject\UserId;

use PDO;

final class RefreshTokenRepository implements RefreshTokenRepositoryInterface {
    public function __construct(private readonly PDO $pdo) {}

    public function save(RefreshToken $refreshToken):void{
        $data = [
            "tokenId"=>$refreshToken->tokenId()->value(),
            "tokenValue"=>$refreshToken->tokenValue()->value(),
            "refreshTokenExpiration"=>$refreshToken->refreshTokenExpiration()->value()->format("Y-m-d H:i:s"),
            "userId"=>$refreshToken->userId()->value(),
            "userAgent"=>$refreshToken->userAgent()
        ];
        $sql = "INSERT INTO refresh_tokens (tokenId, tokenValue, refreshTokenExpiration, userId, userAgent)
                VALUES (:tokenId, :tokenValue, :refreshTokenExpiration, :userId, :userAgent)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    public function findByTokenValue(TokenValue $tokenValue):?RefreshToken{
        $data = [
            "tokenValue"=>$tokenValue->value()
        ];
        $sql = "SELECT * FROM refresh_tokens WHERE tokenValue = :tokenValue LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $refreshToken = $stmt->fetch(PDO::FETCH_ASSOC);
        if($refreshToken){
            return self::reconstitute($refreshToken);
        }else{
            return null;
        }
    }

    public function delete(TokenId $tokenId):void{
        $data = [
            "tokenId"=>$tokenId->value()
        ];
        $sql = "DELETE FROM refresh_tokens WHERE tokenId = :tokenId LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    public function deleteAll(UserId $userId):void{
        $data = [
            "userId"=>$userId->value()
        ];
        $sql = "DELETE FROM refresh_tokens WHERE userId = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    private static function reconstitute(array $refreshToken):RefreshToken {
        return RefreshToken::reconstitute(
            TokenId::fromString($refreshToken["tokenId"]),
            TokenValue::fromString($refreshToken["tokenValue"]),
            RefreshTokenExpiration::fromString($refreshToken["refreshTokenExpiration"]),
            UserId::fromString($refreshToken["userId"]),
            $refreshToken["userAgent"]
        );
    }
}