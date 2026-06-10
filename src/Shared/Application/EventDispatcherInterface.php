<?php

namespace App\Shared\Application;
use App\Shared\Domain\Event\DomainEventInterface;

interface EventDispatcherInterface {
    public function dispatch(DomainEventInterface $event) : void ;
}