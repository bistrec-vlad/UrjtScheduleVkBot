<?php
require_once __DIR__ . "/../../config/vkApi.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/botStrings.php";

require_once __DIR__ . "/../vendor/autoload.php";

require_once __DIR__ . "/../apiClients/VkBotApiClient.php";
require_once __DIR__ . "/../repositories/VkBotRepository.php";
require_once __DIR__ . "/../repositories/SqlUserRepository.php";
require_once __DIR__ . "/../repositories/SqlSubscriptionRepository.php";
require_once __DIR__ . "/../repositories/SqlOrderRepository.php";
require_once __DIR__ . "/../repositories/SqlLogRepository.php";
require_once __DIR__ . "/../repositories/SqlScheduleFileRepository.php";
require_once __DIR__ . "/../entities/VkInlineKeyboard.php";
require_once __DIR__ . "/../entities/VkInlineButton.php";
require_once __DIR__ . "/../Logger.php";

use VK\Client\VKApiClient;

$vkApiClient = new VKApiClient(API_VERSION);
$botApiClient = new VkBotApiClient($vkApiClient, TOKEN);

$pdo = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
$botRepo = new VkBotRepository(
    new SqlUserRepository($pdo, SQL_USERS_TABLE_NAME),
    new SqlSubscriptionRepository($pdo, SQL_SUBSCRIPTIONS_TABLE_NAME),
    new SqlOrderRepository($pdo, SQL_ORDERS_TABLE_NAME),
    new SqlLogRepository($pdo, SQL_LOGS_TABLE_NAME),
    new SqlScheduleFileRepository($pdo, SQL_SCHEDULE_FILES_TABLE_NAME),
);

$userRepo = $botRepo->getUserRepository();
$subscriptionRepo = $botRepo->getSubscriptionRepository();

$logger = new Logger($botRepo->getLogRepository());

$needingRenewalSubscriptions = $subscriptionRepo->findAllNeedingRenewal();

if (!isset($needingRenewalSubscriptions)) {
    exit(0);
}

$keyboard = new VkInlineKeyboard();
$keyboard->addRow([
    new VkInlineButton(
        "➡️ Перейти к оплате",
        json_encode(["button" => "moveToPayment"], JSON_UNESCAPED_UNICODE),
        "primary",
    ),
]);

foreach ($needingRenewalSubscriptions as $sub) {
    if ($sub->isNoneType() || $sub->isUnlimitedType()) {
        continue;
    }

    $user = $userRepo->findById($sub->getUserId());

    $botApiClient->sendKeyboardMessage(
        $user->getChatId(),
        NEEDING_RENEWAL_MESSAGE,
        json_encode($keyboard->getKeyboard()),
        3,
    );

    $subscriptionRepo->updateType($sub->getId(), "none");
}

echo "ok";
