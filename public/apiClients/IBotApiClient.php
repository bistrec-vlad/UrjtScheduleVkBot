<?php

interface IBotApiClient
{
    public function sendMessage(int $chatId, string $text, int $retries);
    public function sendDocument(
        int $chatId,
        string $attachment,
        string $text,
        int $retries,
    );
}
