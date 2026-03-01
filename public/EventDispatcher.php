<?php

require_once __DIR__ . "/eventHandlers/IEventHandler.php";

class EventDispatcher
{
    private array $handlers = [];

    public function registerHandler(string $eventType, IEventHandler $handler)
    {
        $this->handlers[$eventType] = $handler;
    }

    public function dispatch($eventData)
    {
        $type = $eventData["type"] ?? null;

        if (isset($this->handlers[$type])) {
            $this->handlers[$type]->handle($eventData);
        }
    }
}
