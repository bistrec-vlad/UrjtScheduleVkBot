<?php

require_once __DIR__ . "/../../config/vkApi.php";

require_once __DIR__ . "/../apiClients/VkBotApiClient.php";

require_once __DIR__ . "/IEventHandler.php";

use VK\Client\VKApiClient;

class StartCommandEventHandler implements IEventHandler
{
    public function handle($eventData)
    {
        $vkApiClient = new VKApiClient(API_VERSION);
        $botApiClient = new VkBotApiClient($vkApiClient, TOKEN);

        $message = $eventData["object"]["message"];

        $botApiClient->sendMessage($message["from_id"], $message["text"]);
    }
}
