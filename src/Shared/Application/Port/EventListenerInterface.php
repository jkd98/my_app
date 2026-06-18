<?php

namespace App\Shared\Application\Port;

use App\Shared\Domain\Event\DomainEventInterface;


interface EventListenerInterface {
    public function handle(DomainEventInterface $domainEvent):void;
}