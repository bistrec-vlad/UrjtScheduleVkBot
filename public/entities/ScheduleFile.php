<?php

class ScheduleFile
{
    private $id;
    private $url;
    private $size;
    private $lastModified;

    public function __construct(
        string $url,
        int $size,
        int $lastModified,
        int $id = -1,
    ) {
        $this->id = $id;
        $this->size = $size;
        $this->lastModified = $lastModified;
        $this->url = $url;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getLastModified()
    {
        return $this->lastModified;
    }
}
