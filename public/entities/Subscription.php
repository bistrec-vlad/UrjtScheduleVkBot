<?php

final class Subscription
{
    private int $id;
    private int $userId;
    private string $type;
    private string $endDate;

    public function __construct(int $userId, string $type, string $endDate = '', int $id = -1) {
        $this->userId = $userId;
        $this->type = $type;
        $this->endDate = $endDate;
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getType() {
        return $this->type;
    }

    public function getEndDate() {
        return $this->endDate;
    }

    public function isNoneType() {
        return $this->type == 'none';
    }

    public function isUnlimitedType() {
        return $this->type == 'unlimited';
    }
}


?>