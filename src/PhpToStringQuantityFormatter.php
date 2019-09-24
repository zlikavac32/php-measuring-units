<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

/**
 * For debugging purposes only. PHPs __toString is not for public eyes
 */
final class PhpToStringQuantityFormatter implements QuantityFormatter
{

    public function format(Quantity $quantity): string
    {
        return (string) $quantity;
    }
}
