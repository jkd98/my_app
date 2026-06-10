<?php 

namespace App\Auth\Domain\Entity;

use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\ValueObject\UserName;
use App\Auth\Domain\ValueObject\LastName;
use App\Auth\Domain\ValueObject\Email;
use App\Auth\Domain\ValueObject\Password;
use DateTimeImmutable;
use App\Auth\Domain\Events\UserRegistered;


final class User {

    private function __construct(
        private readonly UserId $userId,
        private readonly UserName $userName,
        private readonly LastName $lastName,
        private readonly Email $email,
        private readonly Password $pass,
        private readonly bool $isVerified,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt,
        private array $domainEvents = []
    ) {
        
    }

    public static function register (
        UserId $userId,
        UserName $userName,
        LastName $lastName,
        Email $email,
        Password $pass,
        bool $isVerified = false,
    ) : self {
        $now = new DateTimeImmutable();
        $nwUser = new self(
            $userId,
            $userName,
            $lastName,
            $email,
            $pass,
            $isVerified,
            $now,
            $now,
       );
       $nwUser->domainEvents[] = UserRegistered::create($nwUser->email,$nwUser->userName);
       return $nwUser;
    }

    public static function reconstruite
    (
        UserId $userId,
        UserName $userName,
        LastName $lastName,
        Email $email,
        Password $pass,
        bool $isVerified,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) : self {
        return new self(
            $userId,
            $userName,
            $lastName,
            $email,
            $pass,
            $isVerified,
            $createdAt,
            $updatedAt,
       );
    }

    public function pullDomainEvents(){
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}