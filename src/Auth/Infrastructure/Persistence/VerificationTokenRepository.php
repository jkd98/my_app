<?php 

namespace App\Auth\Infrastructure\Persistence;

use App\Auth\Domain\Repository\VerificationTokenRepositoryInterface;
use App\Auth\Domain\ValueObject\TokenId;
use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\ValueObject\TokenExpiration;
use App\Auth\Domain\ValueObject\TokenType;
use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\Entity\VerificationToken;
use PDO;


final class VerificationTokenRepository implements VerificationTokenRepositoryInterface {
    public function __construct(private readonly PDO $pdo){}

    public function save(VerificationToken $data) : void{
        $data = [
            "tokenId"=>$data->tokenId()->value(),
            "tokenValue"=>$data->tokenValue()->value(),
            "tokenExpiration"=>$data->tokenExpiration()->value()->format('Y-m-d H:i:s'),
            "tokenType"=>$data->tokenType()->value,
            "userId"=>$data->userId()->value()
        ];
        $sql = "INSERT INTO verification_tokens (tokenId, tokenValue, tokenExpiration, tokenType, userId)
                VALUES (:tokenId, :tokenValue, :tokenExpiration, :tokenType, :userId)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data); 
    }

    public function findByTokenValue(TokenValue $value): ?VerificationToken{
        $data=[
            "tokenValue" => $value->value()
        ];
        $sql = "SELECT * FROM verification_tokens WHERE tokenValue = :tokenValue LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $verificationToken = $stmt->fetch(PDO::FETCH_ASSOC);
        if($verificationToken){
            return self::reconstitute($verificationToken);
        }else{
            return null;
        }
    }
    public function findByTokenTypeAndUserId(TokenType $tokenType, UserId $userId): ?VerificationToken{
        $data=[
            "tokenType" => $tokenType->value,
            "userId" => $userId->value()
        ];
        $sql = "SELECT * FROM verification_tokens WHERE tokenType = :tokenType AND userId = :userId LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $verificationToken = $stmt->fetch(PDO::FETCH_ASSOC);
        if($verificationToken){
            return self::reconstitute($verificationToken);
        }else{
            return null;
        }
    }
    
    public function delete(TokenId $id):void{
        $data=[
            "tokenId" => $id->value()
        ];
        $sql = "DELETE FROM verification_tokens WHERE tokenId = :tokenId LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }
    
    public function deleteAllByTokenType(UserId $userId,TokenType $tokenType):void{
        $data=[
            "tokenType" => $tokenType->value,
            "userId" => $userId->value()
        ];
        $sql = "DELETE FROM verification_tokens WHERE tokenType = :tokenType AND userId = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    private static function reconstitute(array $verificationToken):VerificationToken{
        return VerificationToken::reconstitute(
            TokenId::fromString($verificationToken["tokenId"]),
            TokenValue::fromString($verificationToken["tokenValue"]),
            TokenExpiration::fromString($verificationToken["tokenExpiration"]),
            TokenType::from($verificationToken["tokenType"]),
            UserId::fromString($verificationToken["userId"])
        );
    }
}