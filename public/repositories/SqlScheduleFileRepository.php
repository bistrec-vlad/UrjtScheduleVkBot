<?php

require_once __DIR__ . "/../entities/ScheduleFile.php";
require_once __DIR__ . "/IScheduleFileRepository.php";

final class SqlScheduleFileRepository implements IScheduleFileRepository
{
    private PDO $pdo;
    private string $tableName;

    public function __construct(PDO $pdo, string $tableName)
    {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
    }

    public function add(ScheduleFile $scheduleFile): int
    {
        $sql = "INSERT INTO `{$this->tableName}` (`url`, `size`, `lastModified`) VALUES (:url, :size, :lastModified)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ":url" => $scheduleFile->getUrl(),
            ":size" => $scheduleFile->getSize(),
            ":lastModified" => $scheduleFile->getLastModified(),
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

    public function deleteAll()
    {
        $sql = "DELETE FROM `{$this->tableName}`";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();
    }

    private function hydrate(array $data): ScheduleFile
    {
        return new ScheduleFile(
            $data["url"] ?? "",
            $data["size"] ?? "",
            $data["lastModified"] ?? "",
            $data["ID_scheduleFile"],
        );
    }
}

?>
