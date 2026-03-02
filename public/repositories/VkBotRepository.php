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

    public function __construct(
        IUserRepository $userRepo,
        ISubscriptionRepository $subscriptionRepo,
        IOrderRepository $orderRepo,
    ) {
        $this->userRepo = $userRepo;
        $this->subscriptionRepo = $subscriptionRepo;
        $this->orderRepo = $orderRepo;
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
}
