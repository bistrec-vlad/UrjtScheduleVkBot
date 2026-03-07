<?php
require_once __DIR__ . "/../../config/vkApi.php";
require_once __DIR__ . "/../../config/database.php";

require_once __DIR__ . "/../Logger.php";
require_once __DIR__ . "/../vendor/autoload.php";

require_once __DIR__ . "/../EventDispatcher.php";

require_once __DIR__ . "/../eventHandlers/MessageNewEventHandler.php";
require_once __DIR__ . "/../apiClients/VkBotApiClient.php";

require_once __DIR__ . "/../repositories/VkBotRepository.php";
require_once __DIR__ . "/../repositories/SqlUserRepository.php";
require_once __DIR__ . "/../repositories/SqlSubscriptionRepository.php";
require_once __DIR__ . "/../repositories/SqlOrderRepository.php";
require_once __DIR__ . "/../repositories/SqlLogRepository.php";
require_once __DIR__ . "/../repositories/SqlScheduleFileRepository.php";

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

echo "ok";

$pdo = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
$botRepo = new VkBotRepository(
    new SqlUserRepository($pdo, SQL_USERS_TABLE_NAME),
    new SqlSubscriptionRepository($pdo, SQL_SUBSCRIPTIONS_TABLE_NAME),
    new SqlOrderRepository($pdo, SQL_ORDERS_TABLE_NAME),
    new SqlLogRepository($pdo, SQL_LOGS_TABLE_NAME),
    new SqlScheduleFileRepository($pdo, SQL_SCHEDULE_FILES_TABLE_NAME),
);

$vkApiClient = new VKApiClient(API_VERSION);
$botApiClient = new VkBotApiClient($vkApiClient, TOKEN);

$userRepo = $botRepo->getUserRepository();
$user = $userRepo->findByChatId($data["object"]["message"]["from_id"]);
$logRepo = $botRepo->getLogRepository();
$logger = new Logger($logRepo);

$logger->logInfo(
    $user ? $user->getId() : null,
    "Start of programm for user " .
        $data["object"]["message"]["from_id"] .
        ". Received callback",
);

// Регистрируем обработчики событий на каждое событие от VK
$eventDispatcher = new EventDispatcher();
$eventDispatcher->registerHandler(
    "message_new",
    new MessageNewEventHandler($botRepo, $botApiClient, $logger),
);

$eventDispatcher->dispatch($data);

$logger->logInfo(
    $user ? $user->getId() : null,
    "End of programm for user " . $data["object"]["message"]["from_id"],
);
