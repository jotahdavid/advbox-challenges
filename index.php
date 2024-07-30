<?php

require_once 'vendor/autoload.php';

use JotahDavid\RssCrawler\Crawler;

function getRSSItemData(DOMXPath $xpath, DOMNode $item): array
{
    $result = [];

    $result['title'] = removeCDATA(trim($xpath->evaluate('string(.//title)', $item)));
    $result['description'] = removeCDATA(trim($xpath->evaluate('string(.//description)', $item)));
    $result['url'] = removeCDATA(trim($xpath->evaluate('string(.//*[self::link])', $item)));
    $result['imageUrl'] = removeCDATA(trim($xpath->evaluate('string(.//image)', $item)));

    return $result;
}

function removeCDATA(string $value): string
{
    $sanitizedValue = str_replace('<![CDATA[', '', $value);
    return str_replace(']]>', '',  $sanitizedValue);
}

function getFirstFiveRSSItems(DOMXPath $xpath): DOMNodeList
{
    return $xpath->query('//item[position() <= 5]');
}

$crawler = new Crawler();

$gazetaXPath = $crawler->createXPathFromUrl('https://www.gazetadopovo.com.br/feed/rss/republica.xml');
$gazetaNewsList = getFirstFiveRSSItems($gazetaXPath);
$gazetaNews = [];

foreach ($gazetaNewsList as $news) {
    $newsData = getRSSItemData($gazetaXPath, $news);

    $newsXPath = $crawler->createXPathFromUrl($newsData['url']);
    $newsContentNodes = $newsXPath->query('//div[contains(@class, "article-body")]/div[contains(@class, "wrapper")]/*[self::p or self::h2]');
    $newsContent = '';

    foreach ($newsContentNodes as $newsContentNode) {
        $newsContent .= "<{$newsContentNode->nodeName}>" . $newsContentNode->textContent . "</{$newsContentNode->nodeName}>" . '\n';
    }

    $gazetaNews[] = [
        'title' => $newsData['title'],
        'description' => $newsData['description'],
        'url' => $newsData['url'],
        'imageUrl' => $newsData['imageUrl'],
        'content' => $newsContent,
    ];
}

$folhaXPath = $crawler->createXPathFromUrl('https://feeds.folha.uol.com.br/emcimadahora/rss091.xml');
$folhaNewsList = getFirstFiveRSSItems($folhaXPath);
$folhaNews = [];

foreach ($folhaNewsList as $news) {
    $newsData = getRSSItemData($folhaXPath, $news);

    $newsXPath = $crawler->createXPathFromUrl($newsData['url']);
    $newsContentNodes = $newsXPath->query('//div[contains(@class, "c-news__body")]/*[((self::p or self::ul) and not(@class)) or (self::h2 and contains(@class, "c-news__subtitle"))]');
    $newsContent = '';

    foreach ($newsContentNodes as $newsContentNode) {
        $newsContent .= "<{$newsContentNode->nodeName}>" . $newsContentNode->textContent . "</{$newsContentNode->nodeName}>" . '\n';
    }

    $folhaNews[] = [
        'title' => $newsData['title'],
        'description' => $newsData['description'],
        'url' => $newsData['url'],
        'imageUrl' => $newsData['imageUrl'],
        'content' => $newsContent,
    ];
}

$response = [
    'gazeta' => $gazetaNews,
    'folha' => $folhaNews,
];

header('Content-Type: application/json');
die(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
