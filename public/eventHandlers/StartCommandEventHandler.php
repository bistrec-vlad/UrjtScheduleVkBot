<?php
require_once __DIR__ . "/../../config/subscription.php";
require_once __DIR__ . "/../../config/botApi.php";
require_once __DIR__ . "/../../config/logs.php";

require_once __DIR__ . "/../entities/Log.php";

require_once __DIR__ . "/../repositories/IBotRepository.php";
require_once __DIR__ . "/../apiClients/IBotApiClient.php";
require_once __DIR__ . "/IEventHandler.php";

require_once __DIR__ . "/../repositories/BotRepositoryAddUserException.php";
require_once __DIR__ . "/../apiClients/BotApiSendMessageException.php";

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

        $subscriptionRepo = $this->botRepo->getSubscriptionRepository();
        $logsRepo = $this->botRepo->getLogRepository();

        $user = null;

        try {
            $user = $this->botRepo->addUserWithTrialSubscription($chatId);
            $subscription = $subscriptionRepo->findByUserId($user->getId());

            $this->botApiClient->sendMessage(
                $chatId,
                SUCCESS_NOTIFICATION_ACTIVATION .
                    "\n\nВаша бесплатная подписка длится до " .
                    date("d.m.Y", strtotime($subscription->getEndDate())),
                SEND_MESSAGE_RETRIES,
            );
        } catch (BotRepositoryAddUserException $e) {
            $this->botApiClient->sendMessage(
                $chatId,
                FAIL_NOTIFICATION_ACTIVATION,
                SEND_MESSAGE_RETRIES,
            );
        } catch (BotApiSendMessageException $e) {
            $logsRepo->add(
                new Log(
                    $user->getId() ?? null,
                    date(LOG_TIME_FORMAT),
                    LOG_ERROR_TYPE,
                    $e->getMessage(),
                ),
            );
        }
    }
}
