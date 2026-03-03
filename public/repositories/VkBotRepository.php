<?php

require_once __DIR__ . "/IBotRepository.php";
require_once __DIR__ . "/IUserRepository.php";
require_once __DIR__ . "/ISubscriptionRepository.php";
require_once __DIR__ . "/IOrderRepository.php";

class VkBotRepository implements IBotRepository
{
    private $userRepo;
    private $subscriptionRepo;
    private $orderRepo;
    private $logRepo;

    public function __construct(
        IUserRepository $userRepo,
        ISubscriptionRepository $subscriptionRepo,
        IOrderRepository $orderRepo,
        ILogRepository $logRepo,
    ) {
        $this->userRepo = $userRepo;
        $this->subscriptionRepo = $subscriptionRepo;
        $this->orderRepo = $orderRepo;
        $this->logRepo = $logRepo;
    }

    public function getUserRepository(): IUserRepository
    {
        return $this->userRepo;
    }

    public function getSubscriptionRepository(): ISubscriptionRepository
    {
        return $this->subscriptionRepo;
    }

    public function getOrderRepository(): IOrderRepository
    {
        return $this->orderRepo;
    }

    public function getLogRepository(): ILogRepository
    {
        return $this->logRepo;
    }
}
