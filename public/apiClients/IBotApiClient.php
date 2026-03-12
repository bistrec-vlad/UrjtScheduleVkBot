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
    public function sendKeyboardMessage(
        int $chatId,
        string $text,
        string $jsonKeyboard,
        int $retries,
    );

    public function editKeyboardMessage(
        int $chatId,
        int $messageId,
        string $text,
        string $jsonKeyboard,
        int $retries,
    );
}
