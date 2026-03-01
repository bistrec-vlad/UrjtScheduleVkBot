<?php
require_once __DIR__ . "/../vendor/autoload.php";

require_once __DIR__ . "/../../config/vkApi.php";

require_once __DIR__ . "/../EventDispatcher.php";

require_once __DIR__ . "/../eventHandlers/MessageNewEventHandler.php";

// Получаем данные
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["type"])) {
    die("error");
}

// Подтверждение сервера
if ($data["type"] == "confirmation") {
    die(CONFIRMATION);
}

// Регистрируем обработчики событий на каждое событие от VK
$eventDispatcher = new EventDispatcher();
$eventDispatcher->registerHandler("message_new", new MessageNewEventHandler());

$eventDispatcher->dispatch($data);

echo "ok";
