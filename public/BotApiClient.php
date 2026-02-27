<?php

interface BotApiClient
{
    public function sendMessage($chatId, $text);
}
