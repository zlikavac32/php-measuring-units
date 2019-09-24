<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

/**
 * For debugging purposes only. PHPs __toString is not for public eyes
 */
final class PhpToStringMeasureUnitFormatter implements MeasureUnitFormatter
{

    public function format(MeasureUnit $measureUnit): string
    {
        return (string) $measureUnit;
    }
}
