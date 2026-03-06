<?php
require_once __DIR__ . "/../Logger.php";
require_once __DIR__ . "/StartCommandEventHandler.php";

require_once __DIR__ . "/../repositories/IBotRepository.php";
require_once __DIR__ . "/../apiClients/IBotApiClient.php";
require_once __DIR__ . "/IEventHandler.php";

class MessageNewEventHandler implements IEventHandler
{
    private $botRepo;
    private $botApiClient;
    private $logger;

    public function __construct(
        IBotRepository $botRepo,
        IBotApiClient $botApiClient,
        Logger $logger,
    ) {
        $this->botRepo = $botRepo;
        $this->botApiClient = $botApiClient;
        $this->logger = $logger;
    }

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
                    $handler = new StartCommandEventHandler(
                        $this->botRepo,
                        $this->botApiClient,
                        $this->logger,
                    );
                }

                $handler->handle($eventData);
                return;
            }
        }
    }
}
