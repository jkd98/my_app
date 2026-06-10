<?php 

namespace App\Auth\Domain\ValueObject;
use App\Shared\Domain\ValueObject\UUIDv7;

final class TokenId {
    private readonly UUIDv7 $value;

    private function __construct(UUIDv7 $strUuid){
        $this->value = $strUuid;
    }

    public static function generate():self {
        $uuid = UUIDv7::generate();
        return new self($uuid);
    }

    public static function fromString(string $str):self {
        return new self(UUIDv7::fromString($str));
    }

    public function equals(TokenId $tokenId):bool {
        return $this->value() === $tokenId->value();
    }

    public function value():string {
        return $this->value->value();
    }
}