<?php

final class User
{
    private int $id;
    private int $chatId;
    private string $email;

    public function __construct(int $chatId, string $email = '', int $id = -1) {
        $this->chatId = $chatId;
        $this->id = $id;
        $this->email = $email;
    }
    
    public function getEmail() {
        return $this->email;
    }

    public function getId() {
        return $this->id;
    }

    public function getChatId() {
        return $this->chatId;
    }
}


?>