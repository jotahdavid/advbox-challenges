<?php

function fibonacci(): void
{
    $x = 0;
    $y = 1;

    echo $x . ', ' . $y . ', ';

    for ($i = 1; $i <= 11; $i++) {
        echo ($x + $y) . ', ';
        $y = $x + $y;
        $x = $y - $x;
    }

    echo PHP_EOL;
}

fibonacci();
