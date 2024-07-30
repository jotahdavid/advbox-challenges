<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <title>Feed RSS</title>

        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap" rel="stylesheet" />

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Poppins', sans-serif;
            }

            main {
                max-width: 45rem;
                width: 95%;
                margin: 1rem auto;
            }

            h1 {
                margin-bottom: 2rem;
                font-size: 2rem;
            }

            img {
                max-width: 100%;
                object-fit: cover;
            }

            .news-portal__title {
                font-size: 1.75rem;
                margin-bottom: 2rem;
                color: rgb(17, 17, 17);
            }

            ul {
                list-style: none;
                display: flex;
                flex-direction: column;
                row-gap: 1rem;
                margin-bottom: 2rem;
            }

            li {
                padding-bottom: 1rem;
                border-bottom: 1px solid #000;
            }

            details {
                padding: 1rem;
                transition: background-color 300ms ease;
            }

            details[open] {
                background-color: #f4f4f4;
                border-radius: 5px;
            }

            summary {
                display: flex;
                flex-direction: column;
            }

            summary p {
                color: rgb(87, 87, 87);
            }

            h3 {
                margin-bottom: 0.75rem;
                font-size: 1.25rem;
                color: rgb(17, 17, 17);
            }

            .content {
                border-top: 1px solid #000;
                margin-top: 1rem;
                padding-top: 1rem;
                line-height: 170%;
                color: rgb(51, 51, 51);
            }

            .content > p {
                margin-bottom: 0.5rem;
            }

            .content > h2 {
                font-size: 1.125rem;
                margin: 2rem 0 1rem;
            }

            .read-more {
                display: block;
                width: fit-content;
                margin: 0.75rem 0 0 auto;
                padding: 0.25rem 0;
                background-color: transparent;
                cursor: pointer;
                border: none;
                text-decoration: underline;
                color: #3c49bf;
                text-underline-offset: 3px;
                font-size: 1rem;
                transition: color 300ms ease;
            }

            .read-more:hover {
                color: #646cb2;
            }
        </style>
    </head>
    <body>
        <main>
            <h1>Últimas notícias</h1>

            <h2 class="news-portal__title">Gazeta do Povo</h2>
            <ul>
                <?php foreach ($gazeta as $news): ?>
                    <li>
                        <details>
                            <summary>
                                <h3><?= $news['title']; ?></h3>
                                <p>
                                    <em><?= strip_tags($news['description'], 'a'); ?></em>
                                </p>
                                <button class="read-more">Ler mais...</button>
                            </summary>
                            <article class="content">
                                <?= $news['content']; ?>
                                <button class="read-more">Ler mais...</button>
                            </article>
                        </details>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h2 class="news-portal__title">Folha de SP</h2>
            <ul>
                <?php foreach ($folha as $news): ?>
                    <li>
                        <details>
                            <summary>
                                <h3><?= $news['title']; ?></h3>
                                <p>
                                    <em><?= strip_tags($news['description'], 'a'); ?></em>
                                </p>
                                <button class="read-more">Ler mais...</button>
                            </summary>
                            <article class="content">
                                <?= $news['content']; ?>
                                <button class="read-more">Ler mais...</button>
                            </article>
                        </details>
                    </li>
                <?php endforeach; ?>
            </ul>
        </main>

        <script>
            const $detailsList = document.querySelectorAll('details');
            $detailsList.forEach(function ($details) {
                $details.addEventListener('click', (event) => event.preventDefault());

                const $readMoreButtons = $details.querySelectorAll('.read-more');

                $readMoreButtons.forEach(function ($readMoreButton) {
                    $readMoreButton.addEventListener('click', function () {
                        $details.open = !$details.open;

                        $readMoreButtons.forEach(function ($readMoreButton) {
                            $readMoreButton.textContent = $details.open ? 'Fechar' : 'Ler mais...';
                        });
                    });
                });
            });
        </script>
    </body>
</html>
