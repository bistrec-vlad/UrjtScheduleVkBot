<?php

interface IBotApiClient
{
    public function sendMessage($chatId, $text);
}
