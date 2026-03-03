<?php

require_once __DIR__ . "/../entities/Log.php";

interface ILogRepository
{
    public function add(Log $log): int;

    public function getAll(): ?array;

    public function findByUserId(int $userId): ?Log;
    public function findById(int $id): ?Log;
}
