<?php

namespace JotahDavid\RssCrawler\Crawlers;

use JotahDavid\RssCrawler\Crawler;

class STJCrawler extends RSSCrawler
{
    public function __construct(?string $feedRSSUrl = 'https://res.stj.jus.br/hrestp-c-portalp/RSS.xml')
    {
        parent::__construct($feedRSSUrl);

        $this->setNewsContentXPathExpression(
            '//div[@id="ctl00_PlaceHolderMain_ctl06__ControlWrapper_RichHtmlField"]/*[self::p or self::h2]',
        );
    }
}
