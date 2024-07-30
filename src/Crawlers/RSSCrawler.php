<?php

namespace JotahDavid\RssCrawler\Crawlers;

use DOMNode;
use DOMNodeList;
use DOMXPath;
use JotahDavid\RssCrawler\Crawler;

class RSSCrawler
{
    private ?string $feedRSSUrl = null;

    private string $newsContentXPathExpression = '//p';

    public function __construct(?string $feedRSSUrl = null)
    {
        $this->feedRSSUrl = $feedRSSUrl;
    }

    public function getNews(): array
    {
        $crawler = new Crawler();

        $xPath = $crawler->createXPathFromUrl($this->feedRSSUrl);
        $rssItems = $this->getRSSItems($xPath);
        $news = [];

        foreach ($rssItems as $item) {
            $newsData = $this->getRSSItemNodeData($xPath, $item);

            $newsXPath = $crawler->createXPathFromUrl($newsData['url']);
            $newsContent = $this->getNewsContent($newsXPath);

            $news[] = [
                'title' => $newsData['title'],
                'description' => $newsData['description'],
                'url' => $newsData['url'],
                'imageUrl' => $newsData['imageUrl'],
                'content' => $newsContent,
            ];
        }

        return $news;
    }

    protected function getNewsContent(DOMXPath $xPath): string
    {
        $newsContentNodes = $xPath->query($this->newsContentXPathExpression);
        $newsContent = '';

        foreach ($newsContentNodes as $newsContentNode) {
            $newsContent .= "<{$newsContentNode->nodeName}>" . $newsContentNode->textContent . "</{$newsContentNode->nodeName}>";
        }

        return $newsContent;
    }

    protected function getRSSItems(DOMXPath $xPath, int $max = 5): DOMNodeList
    {
        return $xPath->query("//item[position() <= $max]");
    }

    protected function getRSSItemNodeData(DOMXPath $xpath, DOMNode $item): array
    {
        $result = [];

        $result['title'] = $this->removeCDATA(trim($xpath->evaluate('string(.//title)', $item)));
        $result['description'] = $this->removeCDATA(trim($xpath->evaluate('string(.//description)', $item)));
        $result['url'] = $this->removeCDATA(trim($xpath->evaluate('string(.//link)', $item)));
        $result['imageUrl'] = $this->removeCDATA(trim($xpath->evaluate('string(.//image)', $item)));

        return $result;
    }

    protected function removeCDATA(string $value): string
    {
        $sanitizedValue = str_replace('<![CDATA[', '', $value);
        return str_replace(']]>', '',  $sanitizedValue);
    }

    protected function setNewsContentXPathExpression(string $expression): void
    {
        $this->newsContentXPathExpression = $expression;
    }
}
