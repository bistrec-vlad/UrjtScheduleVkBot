<?php

require_once __DIR__ . "/../entities/User.php";

interface IUserRepository
{
    public function add(User $user): int;

    public function getAll(): ?array;

    public function findByChatId(int $chatId): ?User;
    public function findById(int $id): ?User;

    public function updateEmail(int $id, string $value);
}
