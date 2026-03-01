<?php

require_once __DIR__ . "/IEventHandler.php";
require_once __DIR__ . "/StartCommandEventHandler.php";

class MessageNewEventHandler implements IEventHandler
{
    public function handle($eventData)
    {
        // Проверка на полезную нагрузку
        if (isset($eventData["object"]["message"]["payload"])) {
            $payload = json_decode(
                $eventData["object"]["message"]["payload"],
                1,
            );

            if (isset($payload["command"])) {
                $handler = null;

                if ($payload["command"] == "start") {
                    $handler = new StartCommandEventHandler();
                }

                $handler->handle($eventData);
                return;
            }
        }
    }
}
