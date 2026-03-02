<?php

require_once __DIR__ . "/IUserRepository.php";
require_once __DIR__ . "/../entities/User.php";

final class SqlUserRepository implements IUserRepository
{
    private PDO $pdo;
    private string $tableName;

    public function __construct(PDO $pdo, string $tableName)
    {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
    }

    public function add(User $user): int
    {
        $sql = "INSERT INTO `{$this->tableName}` (`chat_id`, `email`) VALUES (:chat_id, :email)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ":chat_id" => $user->getChatId(),
            ":email" => $user->getEmail(),
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

    public function findByChatId(int $chatId): ?User
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE `chat_id` = :chat_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":chat_id" => $chatId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findById(int $id): ?User
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE `ID_user` = :ID_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":ID_user" => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function updateEmail(int $id, string $value)
    {
        $sql = "UPDATE `{$this->tableName}` SET `email`=:email WHERE `ID_user` = :ID_user";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ":email" => $value,
            ":ID_user" => $id,
        ]);
    }

    private function hydrate(array $data): User
    {
        return new User(
            $data["chat_id"],
            $data["email"] ?? "",
            $data["ID_user"],
        );
    }
}

?>
