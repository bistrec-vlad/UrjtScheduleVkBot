<?php

require_once __DIR__ . "/../entities/Order.php";
require_once __DIR__ . "/IOrderRepository.php";

final class SqlOrderRepository implements IOrderRepository
{
    private PDO $pdo;
    private string $tableName;

    public function __construct(PDO $pdo, string $tableName)
    {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
    }

    public function add(Order $order): int
    {
        $sql = "INSERT INTO `{$this->tableName}` (`ID_subscription`, `payment_id`, `payment_message_id`, `cost`, `status`) VALUES (:ID_subscription, :payment_id, :payment_message_id, :cost, :status)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ":ID_subscription" => $order->getSubscriptionId(),
            ":payment_id" => $order->getPaymentId(),
            ":payment_message_id" => $order->getPaymentMessageId(),
            ":cost" => $order->getCost(),
            ":status" => $order->getStatus(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function deleteById(int $id)
    {
        $sql = "DELETE FROM `{$this->tableName}` WHERE ID_order = :ID_order";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ":ID_order" => $id,
        ]);
    }

    public function findByPaymentId(string $paymentId): ?Order
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE `payment_id` = :payment_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":payment_id" => $paymentId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findAllBySubscriptionId(string $subscriptionId): ?array
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE `ID_subscription` = :ID_subscription";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":ID_subscription" => $subscriptionId]);

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

    public function updateStatus(int $id, string $value)
    {
        $sql = "UPDATE `{$this->tableName}` SET `status`=:status WHERE `ID_order` = :ID_order";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ":status" => $value,
            ":ID_order" => $id,
        ]);
    }

    private function hydrate(array $data): Order
    {
        return new Order(
            $data["ID_subscription"] ?? -1,
            $data["payment_id"],
            $data["cost"],
            $data["payment_message_id"],
            $data["status"],
            $data["ID_order"],
        );
    }
}

?>
