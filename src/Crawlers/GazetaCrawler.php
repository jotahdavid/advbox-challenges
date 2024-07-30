<?php

namespace JotahDavid\RssCrawler\Crawlers;

class GazetaCrawler extends RSSCrawler
{
    public function __construct(?string $feedRSSUrl = 'https://www.gazetadopovo.com.br/feed/rss/republica.xml')
    {
        parent::__construct($feedRSSUrl);

        $this->setNewsContentXPathExpression(
            '//div[contains(@class, "article-body")]/div[contains(@class, "wrapper")]/*[self::p or self::h2]',
        );
    }
}
