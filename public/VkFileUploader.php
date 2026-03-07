<?php

use VK\Client\VKApiClient;

class VkFileUploader
{
    private $apiClient;
    private $token;

    public function __construct(VKApiClient $apiClient, string $token)
    {
        $this->apiClient = $apiClient;
        $this->token = $token;
    }

    public function upload(
        int $chatId,
        string $filePath,
        string $title = "",
    ): string {
        $upload_server = $this->apiClient
            ->docs()
            ->getMessagesUploadServer($this->token, [
                "peer_id" => $chatId,
                "type" => "doc",
            ]);

        $uploaded = $this->apiClient
            ->getRequest()
            ->upload($upload_server["upload_url"], "file", $filePath);

        $saved = $this->apiClient->docs()->save($this->token, [
            "file" => $uploaded["file"],
            "title" => $title ?: basename($filePath),
        ]);

        $doc = $saved["doc"];
        $attachment = "doc{$doc["owner_id"]}_{$doc["id"]}";

        // Добавляем access_key, если есть
        if (isset($doc["access_key"])) {
            $attachment .= "_{$doc["access_key"]}";
        }

        return $attachment;
    }
}
