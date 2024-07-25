<?php

namespace JotahDavid\BlogCrawler;

use DOMDocument;
use DOMNodeList;
use DOMXPath;
use Exception;

class HTMLXPath
{
    private DOMXPath $xpath;

    public function __construct()
    {
        $document = new DOMDocument();
        $this->xpath = new DOMXPath($document);
    }

    public function loadHtmlFromUrl(string $url): self
    {
        $html = $this->getHtmlFromUrl($url);
        $this->loadHTML($html);

        return $this;
    }

    public function loadHTML(string $html): self
    {
        $document = new DOMDocument();
        @$document->loadHTML($html);

        $this->xpath = new DOMXPath($document);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function getHtmlFromUrl(string $url): string
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $html = curl_exec($curl);
        curl_close($curl);

        if (!$html) {
            throw new Exception("Não foi possível capturar o corpo da URL: \"{$url}\"");
        }

        return $html;
    }

    public function evaluate(string $expression): mixed
    {
        return $this->xpath->evaluate($expression);
    }

    public function query(string $expression): DOMNodeList|false
    {
        return $this->xpath->query($expression);
    }
}
