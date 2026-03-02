<?php

require_once __DIR__ . "/../entities/Subscription.php";

interface ISubscriptionRepository
{
    public function add(Subscription $subscription): int;

    public function findByUserId(int $userid): ?Subscription;
    public function findById(int $id): ?Subscription;
    public function findAllActiveSubscriptions(): ?array;
    public function findAllNoneTypeSubscriptions(): ?array;
    public function findAllNeedingRenewal(): ?array;

    public function updateType(int $id, string $value);
    public function updateEndDate(int $id, string $value);
}
