<?php

require_once __DIR__ . "/../entities/Order.php";

interface IOrderRepository
{
    public function add(Order $order): int;

    public function deleteById(int $id);

    public function findByPaymentId(string $paymentId): ?Order;
    public function findAllBySubscriptionId(string $subscriptionId): ?array;

    public function updateStatus(int $id, string $value);
}
