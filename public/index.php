<?php

require_once __DIR__ . '/../vendor/autoload.php';

use JotahDavid\RssCrawler\Crawlers\FolhaSPCrawler;
use JotahDavid\RssCrawler\Crawlers\GazetaCrawler;
use JotahDavid\RssCrawler\Crawlers\STJCrawler;
use JotahDavid\RssCrawler\View;

define('PUBLIC_PATH', __DIR__);
define('VIEW_PATH', __DIR__ . '/../views');

$gazetaCrawler = new GazetaCrawler();
$gazetaNews = $gazetaCrawler->getNews();

$folhaSPCrawler = new FolhaSPCrawler();
$folhaSPNews = $folhaSPCrawler->getNews();

$stjCrawler = new STJCrawler();
$stjNews = $stjCrawler->getNews();

$view = new View('home', [
    'gazeta' => $gazetaNews,
    'folha' => $folhaSPNews,
    'stj' => $stjNews,
]);

echo $view->render();
