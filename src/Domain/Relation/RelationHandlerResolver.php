<?php

namespace PhoenixLib\NovaNestedTreeAttachMany\Domain\Relation;

use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use PhoenixLib\NovaNestedTreeAttachMany\Domain\Relation\Handlers\RelationHandler;

class RelationHandlerResolver implements RelationHandlerFactory
{
    private $handlers;

    public function __construct(){
        $this->handlers = new Collection;
    }

    public function make( $relation ): RelationHandler
    {
        $handler = $this->handlers->first(function (RelationHandler $handler) use ($relation){
            $handlerRelation = $handler->relation();
            return $relation instanceof $handlerRelation;
        });

        if($handler)
        {
            return $handler;
        }

        throw new DomainException(sprintf('RelationHandler for relation: %s is not registered', $relation));
    }

    public function register( RelationHandler $handler ): void
    {
        $this->handlers->put($handler->relation(), $handler);
    }

    public function unregister( RelationHandler $handler ): void
    {
        if($this->handlers->has($handler->relation()))
        {
            $this->handlers->forget($handler->relation());
        }
    }

    public function registeredHandlers(): Collection
    {
        return $this->handlers;
    }

    public function unregisterAll(): void
    {
        $this->handlers = new Collection;
    }
}
