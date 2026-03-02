<?php

final class Order
{
    private int $id;
    private int $subscriptionId;
    private string $paymentId;
    private int $paymentMessageId;
    private string $cost;
    private string $status;

    public function __construct(int $subscriptionId, string $paymentId, string $cost, int $paymentMessageId, string $status, int $id = -1) {
        $this->subscriptionId = $subscriptionId;
        $this->paymentId = $paymentId;
        $this->cost = $cost;
        $this->paymentMessageId = $paymentMessageId;
        $this->status = $status;
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getPaymentMessageId() {
        return $this->paymentMessageId;
    }

    public function getSubscriptionId() {
        return $this->subscriptionId;
    }

    public function getPaymentId() {
        return $this->paymentId;
    }

    public function getCost() {
        return $this->cost;
    }

    public function isPendingStatus() {
        return $this->status == 'pending';
    }
}


?>