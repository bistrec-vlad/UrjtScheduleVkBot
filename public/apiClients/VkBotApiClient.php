<?php
require_once __DIR__ . "/../../config/botStrings.php";
require_once __DIR__ . "/../../config/subscription.php";
require_once __DIR__ . "/IBotApiClient.php";

require_once __DIR__ . "/BotApiSendMessageException.php";
require_once __DIR__ . "/BotApiEditMessageException.php";
require_once __DIR__ . "/BotApiSendDocumentException.php";

use VK\Client\VKApiClient;

class VkBotApiClient implements IBotApiClient
{
    private string $token;
    private VKApiClient $apiClient;

    public function __construct(VKApiClient $vkApiClient, string $token)
    {
        $this->apiClient = $vkApiClient;
        $this->token = $token;
    }

    public function sendMessage(int $chatId, string $text, int $retries)
    {
        $exception = null;

        for ($attempt = 1; $attempt <= $retries; $attempt++) {
            try {
                $this->apiClient->messages()->send($this->token, [
                    "peer_id" => $chatId,
                    "message" => $text,
                    "random_id" => random_int(1, 1000000),
                ]);

                return;
            } catch (Exception $e) {
                $exception = $e;

                sleep(2 * $attempt);
            }
        }

        throw new BotApiSendMessageException(
            "Can't send message for user $chatId! Exception: " .
                $exception->getMessage(),
        );
    }

    public function sendDocument(
        int $chatId,
        string $attachment,
        string $text,
        int $retries,
    ) {
        $exception = null;

        for ($attempt = 1; $attempt <= $retries; $attempt++) {
            try {
                $this->apiClient->messages()->send($this->token, [
                    "peer_id" => $chatId,
                    "message" => $text,
                    "attachment" => $attachment,
                    "random_id" => random_int(1, 1000000),
                ]);

                return;
            } catch (Exception $e) {
                $exception = $e;

                sleep(2 * $attempt);
            }
        }

        throw new BotApiSendDocumentException(
            "Can't send document $attachment for user $chatId! Exception: " .
                $exception->getMessage(),
        );
    }

    public function sendKeyboardMessage(
        int $chatId,
        string $text,
        string $jsonKeyboard,
        int $retries,
    ) {
        $exception = null;

        for ($attempt = 1; $attempt <= $retries; $attempt++) {
            try {
                $this->apiClient->messages()->send($this->token, [
                    "peer_id" => $chatId,
                    "message" => $text,
                    "keyboard" => $jsonKeyboard,
                    "random_id" => random_int(1, 1000000),
                ]);

                return;
            } catch (Exception $e) {
                $exception = $e;

                sleep(2 * $attempt);
            }
        }

        throw new BotApiSendMessageException(
            "Can't send keyboard message for user $chatId! Exception: " .
                $exception->getMessage(),
        );
    }

    public function editKeyboardMessage(
        int $chatId,
        int $messageId,
        string $text,
        string $jsonKeyboard,
        int $retries,
    ) {
        $exception = null;

        for ($attempt = 1; $attempt <= $retries; $attempt++) {
            try {
                $this->apiClient->messages()->edit($this->token, [
                    "peer_id" => $chatId,
                    "message" => $text,
                    "conversation_message_id" => $messageId,
                    "keyboard" => $jsonKeyboard,
                    "random_id" => random_int(1, 1000000),
                ]);

                return;
            } catch (Exception $e) {
                $exception = $e;

                sleep(2 * $attempt);
            }
        }

        throw new BotApiEditMessageException(
            "Can't edit keyboard message for user $chatId! Exception: " .
                $exception->getMessage(),
        );
    }
}
