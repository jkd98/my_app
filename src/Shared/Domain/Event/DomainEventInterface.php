<?php

namespace App\Shared\Domain\Event;
use DateTimeImmutable;


interface DomainEventInterface {
    public function eventId():string;
    public function occurredAt():DateTimeImmutable;
}