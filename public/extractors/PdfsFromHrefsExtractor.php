<?php

class PdfsFromHrefsExtractor
{
    public function extract(array $hrefs): ?array
    {
        $pdfs = [];

        foreach ($hrefs as $href) {
            if (stripos($href, ".pdf") !== false) {
                $pdfs[] = $href;
            }
        }

        return !empty($pdfs) ? $pdfs : null;
    }
}
