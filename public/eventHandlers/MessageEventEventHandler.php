<?php
require_once __DIR__ . "/../Logger.php";
require_once __DIR__ . "/MoveToPaymentEventHandler.php";

require_once __DIR__ . "/../repositories/IBotRepository.php";
require_once __DIR__ . "/../apiClients/IBotApiClient.php";
require_once __DIR__ . "/IEventHandler.php";

class MessageEventEventHandler implements IEventHandler
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
        if (isset($eventData["object"]["payload"])) {
            $payload = $eventData["object"]["payload"];

            $handler = null;

            if ($payload["button"] == "moveToPayment") {
                $handler = new MoveToPaymentEventHandler(
                    $this->botRepo,
                    $this->botApiClient,
                    $this->logger,
                );
            }

            $handler->handle($eventData);
        }
    }
}
