<?php

require_once __DIR__ . "/IUserRepository.php";
require_once __DIR__ . "/../entities/Log.php";

final class SqlLogRepository implements ILogRepository
{
    private PDO $pdo;
    private string $tableName;

    public function __construct(PDO $pdo, string $tableName)
    {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
    }

    public function add(Log $log): int
    {
        $sql = "INSERT INTO `{$this->tableName}` (`ID_user`, `timestamp`, `level`, `message`) VALUES (:ID_user, :timestamp, :level, :message)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ":ID_user" => $log->getUserId(),
            ":timestamp" => $log->getTimestamp(),
            ":level" => $log->getLevel(),
            ":message" => $log->getMessage(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function getAll(): ?array
    {
        $sql = "SELECT * FROM `{$this->tableName}`";

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

    public function findByUserId(int $userId): ?Log
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE `ID_user` = :ID_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":ID_user" => $userId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findById(int $id): ?Log
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE `ID_log` = :ID_log";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":ID_log" => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    private function hydrate(array $data): Log
    {
        return new Log(
            $data["ID_user"],
            $data["timestamp"] ?? "",
            $data["level"] ?? "",
            $data["message"] ?? "",
            $data["ID_log"] ?? -1,
        );
    }
}

?>
