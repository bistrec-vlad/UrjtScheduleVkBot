<?php

final class Log
{
    private int $id;
    private int|null $userId;
    private string $timestamp;
    private string $level;
    private string $message;

    public function __construct(
        int|null $userId,
        string $timestamp,
        string $level,
        string $message,
        int $id = -1,
    ) {
        $this->userId = $userId;
        $this->timestamp = $timestamp;
        $this->level = $level;
        $this->message = $message;
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getMessage()
    {
        return $this->message;
    }
}

?>
