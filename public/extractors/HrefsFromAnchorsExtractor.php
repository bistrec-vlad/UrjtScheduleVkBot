<?php
class HrefsFromAnchorsExtractor
{
    public function extract(DOMNodeList $tags): ?array
    {
        $hrefs = [];

        foreach ($tags as $tag) {
            if (!$tag instanceof DOMElement) {
                continue;
            }

            if (!$tag->hasAttribute("href")) {
                continue;
            }

            $hrefs[] = $tag->getAttribute("href");
        }

        return !empty($hrefs) ? $hrefs : null;
    }
}
