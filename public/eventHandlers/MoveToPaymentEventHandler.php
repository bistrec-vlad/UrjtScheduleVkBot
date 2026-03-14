<?php
require_once __DIR__ . "/../../config/botStrings.php";
require_once __DIR__ . "/../../config/payment.php";
require_once __DIR__ . "/../../config/botApi.php";

require_once __DIR__ . "/../entities/VkInlineKeyboard.php";
require_once __DIR__ . "/../entities/VkInlineButton.php";
require_once __DIR__ . "/../Logger.php";

require_once __DIR__ . "/../repositories/IBotRepository.php";
require_once __DIR__ . "/../apiClients/IBotApiClient.php";
require_once __DIR__ . "/IEventHandler.php";

require_once __DIR__ . "/../apiClients/BotApiEditMessageException.php";

class MoveToPaymentEventHandler implements IEventHandler
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
        $chatId = $eventData["object"]["peer_id"];
        $messageId = $eventData["object"]["conversation_message_id"];

        $keyboard = $this->getPaymentKeyboard();

        try {
            $this->botApiClient->editKeyboardMessage(
                $chatId,
                $messageId,
                SUBSCRIPTION_TIME_CHOOSING,
                $keyboard,
                EDIT_MESSAGE_RETRIES,
            );
        } catch (BotApiEditMessageException $e) {
            $user = $this->botRepo->getUserRepository()->findByChatId($chatId);
            $this->logger->logError($user->getId(), $e->getMessage());
        }
    }

    private function getPaymentKeyboard(): string
    {
        $keyboard = new VkInlineKeyboard();

        $keyboard->addRow([
            new VkInlineButton(
                "1 мес. - " . (int) PAYMENT_TYPE_1_AMOUNT_VALUE . " р.",
                json_encode(
                    ["button" => "paymentType1"],
                    JSON_UNESCAPED_UNICODE,
                ),
            ),
        ]);

        $keyboard->addRow([
            new VkInlineButton(
                "3 мес. - " .
                    (int) PAYMENT_TYPE_2_AMOUNT_VALUE .
                    " р. (" .
                    (int) (PAYMENT_TYPE_2_AMOUNT_VALUE / 3) .
                    " р. в мес.)",
                json_encode(
                    ["button" => "paymentType2"],
                    JSON_UNESCAPED_UNICODE,
                ),
            ),
        ]);

        $keyboard->addRow([
            new VkInlineButton(
                "1 год. - " .
                    (int) PAYMENT_TYPE_3_AMOUNT_VALUE .
                    " р. (" .
                    (int) (PAYMENT_TYPE_3_AMOUNT_VALUE / 12) .
                    "р. в мес.)",
                json_encode(
                    ["button" => "paymentType3"],
                    JSON_UNESCAPED_UNICODE,
                ),
            ),
        ]);

        return json_encode($keyboard->getKeyboard());
    }
}
