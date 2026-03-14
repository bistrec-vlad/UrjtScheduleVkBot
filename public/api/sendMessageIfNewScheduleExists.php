<?php

use VK\Client\VKApiClient;

require_once __DIR__ . "/../../config/botParsing.php";
require_once __DIR__ . "/../../config/botStrings.php";
require_once __DIR__ . "/../../config/botApi.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../config/vkApi.php";

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Logger.php";
require_once __DIR__ . "/../parsers/PdfLinkParser.php";
require_once __DIR__ . "/../builders/LinksBuilder.php";
require_once __DIR__ . "/../builders/ScheduleFilesBuilder.php";
require_once __DIR__ . "/../extractors/HrefsFromAnchorsExtractor.php";
require_once __DIR__ . "/../extractors/PdfsFromHrefsExtractor.php";
require_once __DIR__ . "/../repositories/VkBotRepository.php";
require_once __DIR__ . "/../repositories/SqlUserRepository.php";
require_once __DIR__ . "/../repositories/SqlSubscriptionRepository.php";
require_once __DIR__ . "/../repositories/SqlOrderRepository.php";
require_once __DIR__ . "/../repositories/SqlLogRepository.php";
require_once __DIR__ . "/../repositories/SqlScheduleFileRepository.php";

require_once __DIR__ . "/../VkFileUploader.php";
require_once __DIR__ . "/../apiClients/VkBotApiClient.php";

require_once __DIR__ . "/../apiClients/BotApiSendDocumentException.php";

$pdo = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
$botRepo = new VkBotRepository(
    new SqlUserRepository($pdo, SQL_USERS_TABLE_NAME),
    new SqlSubscriptionRepository($pdo, SQL_SUBSCRIPTIONS_TABLE_NAME),
    new SqlOrderRepository($pdo, SQL_ORDERS_TABLE_NAME),
    new SqlLogRepository($pdo, SQL_LOGS_TABLE_NAME),
    new SqlScheduleFileRepository($pdo, SQL_SCHEDULE_FILES_TABLE_NAME),
);

$logger = new Logger($botRepo->getLogRepository());

$logger->logInfo(null, "Start of new schedule file searching");

$scheduleFilesRepo = $botRepo->getScheduleFileRepository();
$subscriptionRepo = $botRepo->getSubscriptionRepository();
$userRepo = $botRepo->getUserRepository();

$vkApiClient = new VKApiClient(API_VERSION);
$botApiClient = new VkBotApiClient($vkApiClient, TOKEN);
$vkFileUploader = new VkFileUploader($vkApiClient, TOKEN);

$pdfLinkParser = new PdfLinkParser(
    new LinksBuilder(),
    new HrefsFromAnchorsExtractor(),
    new PdfsFromHrefsExtractor(),
);

$parsedLinks = $pdfLinkParser->parse(PARSING_URL);

if (!isset($parsedLinks)) {
    exit(0);
}

$filesBuilder = new ScheduleFilesBuilder();
$scheduleFiles = $filesBuilder->build($parsedLinks);

$savedScheduleFiles = $scheduleFilesRepo->getAll();

$maxLastModified = 0;

if (isset($savedScheduleFiles)) {
    foreach ($savedScheduleFiles as $file) {
        if ($file->getLastModified() > $maxLastModified) {
            $maxLastModified = $file->getLastModified();
        }
    }
}

$newScheduleFiles = [];

foreach ($scheduleFiles as $file) {
    if ($file->getLastModified() > $maxLastModified) {
        $isSimilarFile = false;

        foreach ($savedScheduleFiles as $savedFile) {
            if ($file->getUrl() == $savedFile->getUrl()) {
                if ($file->getSize() == $savedFile->getSize()) {
                    $isSimilarFile = true;
                }
            }
        }

        if (!$isSimilarFile) {
            $newScheduleFiles[] = $file;
        }
    }
}

if (empty($newScheduleFiles)) {
    $logger->logInfo(null, "New schedule files is not found");
    $logger->logInfo(null, "End of new schedule file searching");
    exit(0);
}

$scheduleFilesRepo->deleteAll();

foreach ($scheduleFiles as $file) {
    $scheduleFilesRepo->add($file);
}

$activeSubscriptions = $subscriptionRepo->findAllActiveSubscriptions();

if (!isset($activeSubscriptions)) {
    $logger->logInfo(null, "Not have active subscriptions");
    $logger->logInfo(null, "End of new schedule file searching");
    exit(0);
}

foreach ($newScheduleFiles as $file) {
    $fileName = basename($file->getUrl());

    // Скачиваем файл
    $fileContent = @file_get_contents($file->getUrl());
    if ($fileContent === false) {
        $logger->logError(null, "Can't download file: {$file->getUrl()}");
        continue;
    }

    $tempFile =
        sys_get_temp_dir() .
        "/telegram_" .
        md5($file->getUrl()) .
        "_" .
        $fileName;
    $writeResult = file_put_contents($tempFile, $fileContent);

    if ($writeResult === false) {
        $logger->logError(null, "Can't save temp file: $tempFile");
        continue;
    }

    $attachment = "";

    foreach ($activeSubscriptions as $sub) {
        $user = $userRepo->findById($sub->getUserId());

        if (empty($attachment)) {
            $attachment = $vkFileUploader->upload(
                $user->getChatId(),
                $tempFile,
                $fileName,
            );
        }

        try {
            $botApiClient->sendDocument(
                $user->getChatId(),
                $attachment,
                SCHEDULE_FILE_CHANGED_MESSAGE .
                    "\n\nСсылка на файл: {$file->getUrl()}",
                SEND_DOCUMENT_RETRIES,
            );
        } catch (BotApiSendDocumentException $e) {
            $logger->logError($user->getId(), $e->getMessage());
        }
    }

    // Удаляем временный файл
    if (file_exists($tempFile)) {
        if (!unlink($tempFile)) {
            $logger->logWarn(null, "Can't delete temp file: $tempFile");
        }
    }
}

$logger->logInfo(null, "End of new schedule file searching");

echo "ok";
