<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

interface Normalizer
{

    /**
     * Array of components must be sorted by their abbrev
     *
     * @param MeasureUnitComponent[] $components
     *
     * @return array First item is ratio (instance of Decimal) and second is array of
     *     \Zlikavac32\UnitsOfMeasure\NormalizedMeasureUnitComponent instances
     *
     * @throws NormalizeException
     */
    public function normalize(array $components): array;
}
