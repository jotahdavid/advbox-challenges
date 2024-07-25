<?php

require_once 'vendor/autoload.php';

use JotahDavid\BlogCrawler\HTMLXPath;

$htmlXPath = new HTMLXPath();
$htmlXPath->loadHtmlFromUrl('https://blog.advbox.com.br');

$href = $htmlXPath->evaluate('string(//header[@id="header"]//a[contains(./span/text(), "Pontuação por Tarefas")]/@href)');

if (!$href) {
    die('Não foi possível encontrar o link da página "Pontuação por Tarefas.');
}

$htmlXPath->loadHtmlFromUrl($href);
$mostReadArticlesHref = $htmlXPath->query('//section[contains(@class, "aside-posts")]/div[contains(@class, "aside-post")]/a/@href');

$mostReadArticles = [];

foreach ($mostReadArticlesHref as $mostReadArticleHref) {
    $href = $mostReadArticleHref->textContent;

    $articleXPath = new HTMLXPath();
    $articleXPath->loadHtmlFromUrl($href);
    $article = [];

    $article['title'] = trim($articleXPath->evaluate('string(//div[@class="single-post-banner-txt"]/h1)'));
    $article['url'] = $href;
    $article['category'] = trim($articleXPath->evaluate('string(//div[@class="single-post-banner-txt"]/a)'));
    $article['content'] = $articleXPath->evaluate('string(//div[@class="article-content"])');

    $mostReadArticles[] = $article;
}

$blogCategoriesQuery = $htmlXPath->query('//section[contains(@class, "aside-categorias")]/nav/ul/li/a');
$blogCategoriesLevels = [
    '0-100' => [],
    '101-200' => [],
    '200+' => [],
];

foreach ($blogCategoriesQuery as $blogCategory) {
    preg_match('/^(.+)\((\d+)\)$/', $blogCategory->textContent, $matches);

    if (empty($matches)) {
        continue;
    }

    $categoryName = $matches[1];
    $categoryPostsCount = (int) $matches[2];

    if ($categoryPostsCount <= 100) {
        $blogCategoriesLevels['0-100'][$categoryPostsCount] = $categoryName;
    } else if ($categoryPostsCount <= 200) {
        $blogCategoriesLevels['101-200'][$categoryPostsCount] = $categoryName;
    } else {
        $blogCategoriesLevels['200+'][$categoryPostsCount] = $categoryName;
    }
}

$response = [
    'mostReadArticles' => $mostReadArticles,
    'categories' => $blogCategoriesLevels,
];

header('Content-Type: application/json');
die(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
