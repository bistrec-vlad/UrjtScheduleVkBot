<?php
require_once __DIR__ . "/../../config/botStrings.php";
require_once __DIR__ . "/../../config/subscription.php";
require_once __DIR__ . "/IBotApiClient.php";

require_once __DIR__ . "/BotApiSendMessageException.php";

use VK\Client\VKApiClient;

class VkBotApiClient implements IBotApiClient
{
    public string $token;
    public VKApiClient $apiClient;

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
}
