<?php
require_once __DIR__ . "/IBotApiClient.php";

use VK\Client\VKApiClient;

class VkBotApiClient implements IBotApiClient
{
    public string $token;
    public VKApiClient $apiClient;

    public function __construct($vkApiClient, $token)
    {
        $this->apiClient = $vkApiClient;
        $this->token = $token;
    }

    public function sendMessage($chatId, $text)
    {
        $this->apiClient->messages()->send($this->token, [
            "peer_id" => $chatId,
            "message" => $text,
            "random_id" => random_int(1, 1000000),
        ]);
    }
}
