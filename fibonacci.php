<?php

function fibonacci(): array
{
    $sequence = [0, 1];
    for ($i = 1; $i <= 11; $i++) {
        $previous = $sequence[$i - 1];
        $current = $sequence[$i];
        $sequence[] = $previous + $current;
    }
    return $sequence;
}

$fibonacciSequence = fibonacci();
echo join(', ', $fibonacciSequence) . PHP_EOL;
