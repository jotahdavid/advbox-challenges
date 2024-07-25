<?php

namespace JotahDavid\RssCrawler;

use JotahDavid\RssCrawler\Enums\ContentType;
use DOMDocument;
use DOMXPath;

class Crawler
{
    public DOMXPath $xpath;

    public function __construct()
    {
        $document = new DOMDocument();
        $this->xpath = new DOMXPath($document);
    }

    public function createXPathFromUrl(string $url): DOMXPath
    {
        $response = $this->getResponseFromUrl($url);
        $contentType = str_contains($response->getContentType(), 'text/xml') ? ContentType::XML : ContentType::HTML;

        return $this->createXPath($response->getBody(), $contentType);
    }

    public function createXPath(string $content, ContentType $type = ContentType::HTML): DOMXPath
    {
        $document = new DOMDocument();

        if ($type === ContentType::XML) {
            @$document->loadXML($content);
        } else {
            @$document->loadHTML($content);
        }

        return new DOMXPath($document);
    }

    public function getContentFromUrl(string $url): string
    {
        $response = $this->getResponseFromUrl($url);

        return $response->getBody();
    }

    public function getResponseFromUrl(string $url): Response
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        $body = curl_exec($curl);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $statusCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);

        if ($statusCode === 301) {
            $redirectUrl = curl_getinfo($curl, CURLINFO_REDIRECT_URL);

            return $this->getResponseFromUrl($redirectUrl);
        }

        return new Response($body, $contentType);
    }
}
