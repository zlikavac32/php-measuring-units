<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

function sign(float|int $f): int
{
    return $f < 0 ? -1 : ($f > 0 ? 1 : 0);
}

function cmp_string_prefix_desc(string $first, string $second): int
{
    $diff = strlen($first) - strlen($second);

    if ($diff !== 0) {
        return -$diff;
    }

    return $second <=> $first;
}
