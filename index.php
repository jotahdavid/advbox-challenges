<?php

require_once 'vendor/autoload.php';

use JotahDavid\RssCrawler\Crawlers\FolhaSPCrawler;
use JotahDavid\RssCrawler\Crawlers\GazetaCrawler;

$gazetaCrawler = new GazetaCrawler();
$gazetaNews = $gazetaCrawler->getNews();

$folhaSPCrawler = new FolhaSPCrawler();
$folhaSPNews = $folhaSPCrawler->getNews();

$response = [
    'gazeta' => $gazetaNews,
    'folha' => $folhaSPNews,
];

header('Content-Type: application/json');
die(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
