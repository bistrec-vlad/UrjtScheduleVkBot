<?php

require_once __DIR__ . "/IInlineButton.php";

class VkInlineButton implements IInlineButton
{
    private array $button;

    public function __construct(
        string $text,
        string $callback,
        string $color = "secondary",
    ) {
        $this->button = [
            "action" => [
                "type" => "callback",
                "payload" => $callback,
                "label" => $text,
            ],
            "color" => $color,
        ];
    }

    public function getText(): string
    {
        return $this->button["action"]["label"];
    }

    public function getCallback(): string
    {
        return $this->button["action"]["payload"];
    }

    public function getButton(): array
    {
        return $this->button;
    }
}
