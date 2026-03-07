<?php

require_once __DIR__ . "/../builders/LinksBuilder.php";
require_once __DIR__ . "/../extractors/HrefsFromAnchorsExtractor.php";
require_once __DIR__ . "/../extractors/PdfsFromHrefsExtractor.php";

class PdfLinkParser
{
    private $linksBuilder;
    private $hrefsExtractor;
    private $pdfsExtractor;

    public function __construct(
        LinksBuilder $linksBuilder,
        HrefsFromAnchorsExtractor $hrefsExtractor,
        PdfsFromHrefsExtractor $pdfsExtractor,
    ) {
        $this->linksBuilder = $linksBuilder;
        $this->hrefsExtractor = $hrefsExtractor;
        $this->pdfsExtractor = $pdfsExtractor;
    }

    public function parse(string $url): ?array
    {
        $html = file_get_contents($url);

        $dom = new DOMDocument();
        @$dom->loadHTML($html);

        $aTags = $dom->getElementsByTagName("a");

        $hrefs = $this->hrefsExtractor->extract($aTags);

        if (!isset($hrefs)) {
            return null;
        }

        $pdfs = $this->pdfsExtractor->extract($hrefs);

        if (!isset($pdfs)) {
            return null;
        }

        $links = $this->linksBuilder->build($pdfs, $url);

        return isset($links) ? $links : null;
    }
}
