<?php

require_once __DIR__ . "/../config/logs.php";
require_once __DIR__ . "/entities/Log.php";
require_once __DIR__ . "/repositories/ILogRepository.php";

class Logger
{
    private $logRepo;

    public function __construct(ILogRepository $logRepo)
    {
        $this->logRepo = $logRepo;
    }

    public function logError(int|null $userId, $messageText)
    {
        $this->logMessage($userId, LOG_ERROR_TYPE, $messageText);
    }

    public function logWarn(int|null $userId, $messageText)
    {
        $this->logMessage($userId, LOG_WARN_TYPE, $messageText);
    }

    public function logInfo(int|null $userId, string $messageText)
    {
        $this->logMessage($userId, LOG_INFO_TYPE, $messageText);
    }

    private function logMessage(
        int|null $userId,
        string $logType,
        string $messageText,
    ) {
        $this->logRepo->add(
            new Log($userId, date(LOG_TIME_FORMAT), $logType, $messageText),
        );
    }
}
