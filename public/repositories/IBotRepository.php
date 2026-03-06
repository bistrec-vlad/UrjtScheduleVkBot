<?php
require_once __DIR__ . "/../entities/User.php";

require_once __DIR__ . "/IUserRepository.php";
require_once __DIR__ . "/ISubscriptionRepository.php";
require_once __DIR__ . "/IOrderRepository.php";
require_once __DIR__ . "/ILogRepository.php";

interface IBotRepository
{
    public function addUserWithTrialSubscription(int $chatId): User;

    public function getUserRepository(): IUserRepository;
    public function getSubscriptionRepository(): ISubscriptionRepository;
    public function getOrderRepository(): IOrderRepository;
    public function getLogRepository(): ILogRepository;
}
