<?php

namespace App\Auth\Domain\Events;

use App\Shared\Domain\Event\DomainEventInterface;
use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\ValueObject\Email;
use App\Auth\Domain\ValueObject\UserName;
use App\Shared\Domain\ValueObject\UUIDv7;
use App\Auth\Domain\ValueObject\TokenValue;
use DateTimeImmutable;


final class PasswordRecoveryRequested implements DomainEventInterface {
    private function __construct(
        private readonly string $eventId,
        private readonly DateTimeImmutable $occurredAt,
        private readonly UserId $userRequestId,
        private readonly Email $email,
        private readonly UserName $userName,
        private readonly TokenValue $tokenValue 
    ){}

    public static function create(UserId $userId,Email $email,UserName $userName,TokenValue $tokenValue) : self {
        $eventId = UUIDv7::generate();
        $now = new DateTimeImmutable();
        return new self($eventId->value(),$now,$userId,$email,$userName,$tokenValue);
    }

    public function eventId() : string {
        return $this->eventId;
    }

    public function occurredAt() : DateTimeImmutable {
        return $this->occurredAt;
    }

    public function userRequestId() : UserId {
        return $this->userRequestId;
    }

    public function email() : Email {
        return $this->email;
    }

    public function userName() : UserName {
        return $this->userName;
    }

    public function tokenRecoveryValue(): TokenValue {
        return $this->tokenValue;
    }
}