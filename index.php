<?php

require_once 'vendor/autoload.php';

use JotahDavid\RssCrawler\Crawler;

$crawler = new Crawler();
$gazetaXPath = $crawler->createXPathFromUrl('https://www.gazetadopovo.com.br/feed/rss/republica.xml');
$folhaNewsList = $gazetaXPath->query('//item[position() <= 5]');
$gazetaNews = [];

foreach ($folhaNewsList as $folhaNews) {
    $newsTitle = trim($gazetaXPath->evaluate('string(.//title)', $folhaNews));
    $newsDescription = trim($gazetaXPath->evaluate('string(.//description)', $folhaNews));
    $newsUrl = trim($gazetaXPath->evaluate('string(.//link)', $folhaNews));
    $newsImageUrl = trim($gazetaXPath->evaluate('string(.//image)', $folhaNews));

    $newsXPath = $crawler->createXPathFromUrl($newsUrl);
    $newsContentNodes = $newsXPath->query('//div[contains(@class, "article-body")]/div[contains(@class, "wrapper")]/*[self::p or self::h2]');
    $newsContent = '';

    foreach ($newsContentNodes as $newsContentNode) {
        $newsContent .= "<{$newsContentNode->nodeName}>" . $newsContentNode->textContent . "</{$newsContentNode->nodeName}>" . '\n';
    }

    $gazetaNews[] = [
        'title' => $newsTitle,
        'description' => $newsDescription,
        'url' => $newsUrl,
        'imageUrl' => $newsImageUrl,
        'content' => $newsContent,
    ];
}

header('Content-Type: application/json');
die(json_encode(['gazeta' => $gazetaNews], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
