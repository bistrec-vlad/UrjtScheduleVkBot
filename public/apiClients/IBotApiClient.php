<?php

interface IBotApiClient
{
    public function sendMessage(int $chatId, string $text, int $retries);
}
