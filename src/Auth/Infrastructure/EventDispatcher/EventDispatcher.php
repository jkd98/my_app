<?php

namespace App\Auth\Infrastructure\EventDispatcher;
use App\Shared\Application\Port\EventDispatcherInterface;
use App\Shared\Domain\Event\DomainEventInterface;
use App\Shared\Application\Port\EventListenerInterface;

final class EventDispatcher implements EventDispatcherInterface {
    private array $listeners = [];

    public function dispatch(DomainEventInterface $event):void {
        if( !isset($this->listeners[$event::class]) ) return;

        foreach($this->listeners[$event::class] as $listener){
            $listener->handle($event);
        }
    }

    public function addListener(string $eventName, EventListenerInterface $listener):void {
        if( !isset($this->listeners[$eventName]) ){
            $nwListsners = [...$this->listeners,$eventName=>[]];
            $this->listeners=$nwListsners;
        }
        $nwListsners = [...$this->listeners,$eventName=>[...$this->listeners[$eventName],$listener]];
        $this->listeners=$nwListsners;
    }

}