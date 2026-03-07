<?php

require_once __DIR__ . "/../entities/ScheduleFile.php";

interface IScheduleFileRepository
{
    public function add(ScheduleFile $scheduleFile): int;

    public function getAll(): ?array;

    public function deleteAll();
}
