<?php
require_once __DIR__ . "/../../config/subscription.php";
require_once __DIR__ . "/../../config/botApi.php";
require_once __DIR__ . "/../../config/logs.php";

require_once __DIR__ . "/IEventHandler.php";

require_once __DIR__ . "/../repositories/IBotRepository.php";
require_once __DIR__ . "/../entities/Log.php";
require_once __DIR__ . "/../apiClients/IBotApiClient.php";
require_once __DIR__ . "/../apiClients/IBotApiSendMessageException.php";

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
        $logsRepo = $this->botRepo->getLogRepository();

        $user = $userRepo->findByChatId($chatId);

        if (!isset($user)) {
            $userId = $userRepo->add(new User($chatId));
            $endDate = TRIAL_END_DATE;

            $subscriptionRepo->add(
                new Subscription($userId, TRIAL_NAME, $endDate),
            );

            try {
                $this->botApiClient->sendMessage(
                    $chatId,
                    $text,
                    SEND_MESSAGE_RETRIES,
                );
            } catch (IBotApiSendMessageException $e) {
                $logsRepo->add(
                    new Log(
                        $userId,
                        date(LOG_TIME_FORMAT),
                        LOG_ERROR_TYPE,
                        $e->getMessage(),
                    ),
                );
            }

            error_log("ne ok");
        } else {
            error_log("ok");
        }
    }
}
