<?php

require_once __DIR__ . "/../entities/Subscription.php";
require_once __DIR__ . "/ISubscriptionRepository.php";

final class SqlSubscriptionRepository implements ISubscriptionRepository
{
    private PDO $pdo;
    private string $tableName;

    public function __construct(PDO $pdo, string $tableName)
    {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
    }

    public function add(Subscription $subscription): int
    {
        $sql = "INSERT INTO `{$this->tableName}` (`ID_user`, `type`, `end_date`) VALUES (:ID_user, :type, :end_date)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ":ID_user" => $subscription->getUserId(),
            ":type" => $subscription->getType(),
            ":end_date" => $subscription->getEndDate(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findById(int $id): ?Subscription
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE `ID_subscription` = :ID_subscription";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":ID_subscription" => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findByUserId(int $userid): ?Subscription
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE `ID_user` = :ID_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":ID_user" => $userid]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findAllActiveSubscriptions(): ?array
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE `type` != 'none'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $res = [];

        foreach ($data as $item) {
            $res[] = $this->hydrate($item);
        }

        return $res;
    }

    public function findAllNoneTypeSubscriptions(): ?array
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE `type` = 'none'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $res = [];

        foreach ($data as $item) {
            $res[] = $this->hydrate($item);
        }

        return $res;
    }

    public function findAllNeedingRenewal(): ?array
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE NOW() >= end_date";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $res = [];

        foreach ($data as $item) {
            $res[] = $this->hydrate($item);
        }

        return $res;
    }

    public function updateType(int $id, string $value)
    {
        $sql = "UPDATE `{$this->tableName}` SET `type`=:type WHERE `ID_subscription` = :ID_subscription";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ":type" => $value,
            ":ID_subscription" => $id,
        ]);
    }

    public function updateEndDate(int $id, string $value)
    {
        $sql = "UPDATE `{$this->tableName}` SET `end_date`=:end_date WHERE `ID_subscription` = :ID_subscription";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ":end_date" => $value,
            ":ID_subscription" => $id,
        ]);
    }

    private function hydrate(array $data): Subscription
    {
        return new Subscription(
            $data["ID_user"],
            $data["type"],
            $data["end_date"] ?? "",
            $data["ID_subscription"],
        );
    }
}

?>
