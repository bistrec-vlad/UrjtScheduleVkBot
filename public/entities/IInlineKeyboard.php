<?php

interface IInlineKeyboard
{
    public function addRow(array $buttons): IInlineKeyboard;
    public function getKeyboard(): array;
}
