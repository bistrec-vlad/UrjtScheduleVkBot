<?php

require_once __DIR__ . "/../entities/ScheduleFile.php";

class ScheduleFilesBuilder
{
    public function build(array $links): ?array
    {
        $files = [];

        foreach ($links as $link) {
            $headers = get_headers($link, 1);

            $files[] = new ScheduleFile(
                $link,
                $headers["Content-Length"],
                strtotime($headers["Last-Modified"]),
            );
        }

        return !empty($files) ? $files : null;
    }
}
