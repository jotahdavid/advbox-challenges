<?php

namespace JotahDavid\RssCrawler\Crawlers;

class FolhaSPCrawler extends RSSCrawler
{
    public function __construct(?string $feedRSSUrl = 'https://feeds.folha.uol.com.br/emcimadahora/rss091.xml')
    {
        parent::__construct($feedRSSUrl);

        $this->setNewsContentXPathExpression(
            '//div[contains(@class, "c-news__body")]/*[((self::p or self::ul) and not(@class)) or (self::h2 and contains(@class, "c-news__subtitle"))]',
        );
    }
}
