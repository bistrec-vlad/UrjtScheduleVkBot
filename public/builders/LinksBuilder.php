<?php

class LinksBuilder
{
    public function build(array $paths, string $baseUrl): ?array
    {
        $links = [];

        $base = parse_url($baseUrl);
        foreach ($paths as $path) {
            $links[] = $base["scheme"] . "://" . $base["host"] . $path;
        }

        return !empty($links) ? $links : null;
    }
}
