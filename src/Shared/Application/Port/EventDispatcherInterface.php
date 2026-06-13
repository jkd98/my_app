<?php

namespace App\Shared\Application\Port;
use App\Shared\Domain\Event\DomainEventInterface;

interface EventDispatcherInterface {
    public function dispatch(DomainEventInterface $event) : void ;
}