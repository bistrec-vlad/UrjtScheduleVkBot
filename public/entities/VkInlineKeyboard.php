<?php

require_once __DIR__ . "/IInlineKeyboard.php";
require_once __DIR__ . "/VkInlineButton.php";

require_once __DIR__ . "/InvalidButtonSettingsException.php";

final class VkInlineKeyboard implements IInlineKeyboard
{
    private array $keyboard;

    public function __construct()
    {
        $this->keyboard = [
            "inline" => true,
            "buttons" => [],
        ];
    }

    public function addRow(array $buttons): IInlineKeyboard
    {
        $row = [];

        foreach ($buttons as $button) {
            if (empty($button->getText())) {
                throw new InvalidButtonSettingsException(
                    "Invalid text for button",
                );
            }
            if (empty($button->getCallback())) {
                throw new InvalidButtonSettingsException(
                    "Invalid callback for button",
                );
            }

            $row[] = $button->getButton();
        }

        $this->keyboard["buttons"][] = $row;
        return $this;
    }

    public function getKeyboard(): array
    {
        return $this->keyboard;
    }
}
