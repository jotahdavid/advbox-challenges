<?php

function getHtmlBodyFromUrl(string $url): string|bool
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $html = curl_exec($curl);
    curl_close($curl);

    return $html;
}

function makeDOMXPathFromHtml(string $html): DOMXPath
{
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    return new DOMXPath($dom);
}

$blogHtml = getHtmlBodyFromUrl('https://blog.advbox.com.br');
$xpath = makeDOMXPathFromHtml($blogHtml);

$href = $xpath->evaluate('string(//header[@id="header"]//a[contains(./span/text(), "Pontuação por Tarefas")]/@href)');

if (!$href) {
    die('Não foi possível encontrar o link da página "Pontuação por Tarefas.');
}

$taskScoreHtml = getHtmlBodyFromUrl($href);
$xpath = makeDOMXPathFromHtml($taskScoreHtml);

$mostReadArticlesHref = $xpath->query('//section[contains(@class, "aside-posts")]/div[contains(@class, "aside-post")]/a/@href');
$mostReadArticles = [];

foreach ($mostReadArticlesHref as $mostReadArticleHref) {
    $href = $mostReadArticleHref->textContent;

    $articleHtml = getHtmlBodyFromUrl($href);
    $articleXPath = makeDOMXPathFromHtml($articleHtml);
    $article = [];

    $article['title'] = trim($articleXPath->evaluate('string(//div[@class="single-post-banner-txt"]/h1)'));
    $article['url'] = $href;
    $article['category'] = trim($articleXPath->evaluate('string(//div[@class="single-post-banner-txt"]/a)'));
    $article['content'] = $articleXPath->evaluate('string(//div[@class="article-content"])');

    $mostReadArticles[] = $article;
}

$blogCategoriesQuery = $xpath->query('//section[contains(@class, "aside-categorias")]/nav/ul/li/a');
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
