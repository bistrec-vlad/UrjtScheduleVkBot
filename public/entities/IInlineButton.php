<?php

interface IInlineButton
{
    public function getText(): string;
    public function getCallback(): string;
    public function getButton(): array;
}
