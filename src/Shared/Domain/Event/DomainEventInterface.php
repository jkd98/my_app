<?php

namespace App\Shared\Domain\Event;
use DateTimeImmutable;


Interface DomainEventInterface {
    public function eventId():string;
    public function occurredAt():DateTimeImmutable;
}