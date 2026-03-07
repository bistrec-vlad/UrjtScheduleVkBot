<?php

require_once __DIR__ . "/../../config/subscription.php";

require_once __DIR__ . "/IBotRepository.php";
require_once __DIR__ . "/IUserRepository.php";
require_once __DIR__ . "/ISubscriptionRepository.php";
require_once __DIR__ . "/IOrderRepository.php";

require_once __DIR__ . "/BotRepositoryAddUserException.php";

class VkBotRepository implements IBotRepository
{
    private $userRepo;
    private $subscriptionRepo;
    private $orderRepo;
    private $logRepo;
    private $scheduleFileRepo;

    public function __construct(
        IUserRepository $userRepo,
        ISubscriptionRepository $subscriptionRepo,
        IOrderRepository $orderRepo,
        ILogRepository $logRepo,
        IScheduleFileRepository $scheduleFileRepo,
    ) {
        $this->userRepo = $userRepo;
        $this->subscriptionRepo = $subscriptionRepo;
        $this->orderRepo = $orderRepo;
        $this->logRepo = $logRepo;
        $this->scheduleFileRepo = $scheduleFileRepo;
    }

    public function addUserWithTrialSubscription(int $chatId): User
    {
        $user = $this->userRepo->findByChatId($chatId);

        if (isset($user)) {
            throw new BotRepositoryAddUserException("User is already exists!");
        }

        $user = new User($chatId);
        $userId = $this->userRepo->add($user);

        $this->subscriptionRepo->add(
            new Subscription($userId, TRIAL_NAME, TRIAL_END_DATE),
        );

        return $this->userRepo->findById($userId);
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

    public function getScheduleFileRepository(): IScheduleFileRepository
    {
        return $this->scheduleFileRepo;
    }
}
