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
        private UserName $userName,
        private LastName $lastName,
        private readonly Email $email,
        private Password $pass,
        private bool $isVerified,
        private readonly DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
        private array $domainEvents = []
    ) {
        
    }

    public static function register (
        UserId $userId,
        UserName $userName,
        LastName $lastName,
        Email $email,
        Password $pass,
    ) : self {
        $now = new DateTimeImmutable();
        $nwUser = new self(
            $userId,
            $userName,
            $lastName,
            $email,
            $pass,
            false,
            $now,
            $now,
       );
       $nwUser->domainEvents[] = UserRegistered::create($nwUser->userId(), $nwUser->email(),$nwUser->userName());
       return $nwUser;
    }

    public static function reconstitute
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

    public function pullDomainEvents() : array {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    public function confirmAccount() : void {
        $this->isVerified = true;
        $this->userUpdatedAt();
    }

    public function changePassword(Password $nwPass) : void {
        $this->pass = $nwPass;
        $this->userUpdatedAt();
    }

    public function correctUserName(UserName $nwUserName) : void {
        $this->userName = $nwUserName;
        $this->userUpdatedAt();
    }

    public function correctLastNames(LastName $nwLastName) : void {
        $this->lastName = $nwLastName;
        $this->userUpdatedAt();
    }

    private function userUpdatedAt() : void {
        $now =  new DateTimeImmutable();
        $this->updatedAt = $now;
    }

    public function userId() : UserId { return $this->userId; }
    public function userName(): UserName { return $this->userName; }
    public function lastName() : LastName { return $this->lastName; }
    public function email() : Email { return $this->email; }
    public function password() : Password { return $this->pass; }
    public function isVerified() : bool { return $this->isVerified; }
    public function createdAt() : DateTimeImmutable { return $this->createdAt; }
    public function updatedAt(): DateTimeImmutable { return $this->updatedAt; }
}