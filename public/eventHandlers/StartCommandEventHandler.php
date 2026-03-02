<?php
require_once __DIR__ . "/../../config/subscription.php";

require_once __DIR__ . "/IEventHandler.php";

require_once __DIR__ . "/../repositories/IBotRepository.php";
require_once __DIR__ . "/../apiClients/IBotApiClient.php";

class StartCommandEventHandler implements IEventHandler
{
    private $botRepo;
    private $botApiClient;

    public function __construct(
        IBotRepository $botRepo,
        IBotApiClient $botApiClient,
    ) {
        $this->botRepo = $botRepo;
        $this->botApiClient = $botApiClient;
    }

    public function handle($eventData)
    {
        $message = $eventData["object"]["message"];
        $chatId = $message["from_id"];
        $text = $message["text"];

        $userRepo = $this->botRepo->getUserRepository();
        $subscriptionRepo = $this->botRepo->getSubscriptionRepository();

        $user = $userRepo->findByChatId($chatId);

        if (!isset($user)) {
            $userId = $userRepo->add(new User($chatId));
            $endDate = TRIAL_END_DATE;

            $subscriptionRepo->add(
                new Subscription($userId, TRIAL_NAME, $endDate),
            );

            $this->botApiClient->sendMessage($chatId, $text);

            error_log("ne ok");
        } else {
            error_log("ok");
        }
    }
}
