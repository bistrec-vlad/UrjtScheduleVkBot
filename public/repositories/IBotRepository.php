<?php

require_once __DIR__ . "/IUserRepository.php";
require_once __DIR__ . "/ISubscriptionRepository.php";
require_once __DIR__ . "/IOrderRepository.php";

interface IBotRepository
{
    public function getUserRepository(): IUserRepository;
    public function getSubscriptionRepository(): ISubscriptionRepository;
    public function getOrderRepository(): IOrderRepository;
}
