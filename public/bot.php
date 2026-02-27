<?php
require_once "vendor/autoload.php";
require_once __DIR__ . "/../config/vkApi.php";

use VK\Client\VKApiClient;

// Получаем данные
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["type"])) {
    die("error");
}

// Подтверждение сервера
if ($data["type"] == "confirmation") {
    die(CONFIRMATION);
}

// Обработка нового сообщения
if ($data["type"] == "message_new") {
    $vk = new VKApiClient("5.131");
    $message = $data["object"]["message"];

    // Эхо-ответ
    $vk->messages()->send(TOKEN, [
        "peer_id" => $message["peer_id"],
        "message" => "Эхо: " . $message["text"],
        "random_id" => random_int(1, 1000000),
    ]);
}

echo "ok";
