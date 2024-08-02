<?php

namespace JotahDavid\RssCrawler;

use JotahDavid\RssCrawler\Enums\ContentType;
use DOMDocument;
use DOMXPath;

class Crawler
{
    public function createXPathFromUrl(string $url): DOMXPath
    {
        $response = $this->getResponseFromUrl($url);
        $contentType = str_contains($response->getContentType(), 'xml')
            ? ContentType::XML
            : ContentType::HTML;

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
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $body = curl_exec($curl);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        curl_close($curl);

        return new Response($body, $contentType);
    }
}
