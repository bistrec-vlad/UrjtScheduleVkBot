<?php
require_once "vendor/autoload.php";
require_once __DIR__ . "/../config/vkApi.php";
require_once __DIR__ . "/VkBotApiClient.php";

use VK\Client\VKApiClient;

// Получаем данные
$data = json_decode(file_get_contents("php://input"), true);
// error_log(print_r($data, 1));

if (!$data || !isset($data["type"])) {
    die("error");
}

// Подтверждение сервера
if ($data["type"] == "confirmation") {
    die(CONFIRMATION);
}

if (isset($data["object"]["message"]["payload"])) {
    $payload = json_decode($data["object"]["message"]["payload"], 1);

    if ($payload["command"] == "start") {
    }
}

$vkApiClient = new VKApiClient("5.199");
$botApiClient = new VkBotApiClient($vkApiClient, TOKEN);

// Обработка нового сообщения
if ($data["type"] == "message_new") {
    $message = $data["object"]["message"];

    $botApiClient->sendMessage($message["peer_id"], $message["text"]);
}

echo "ok";
