<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

interface NormalizedMeasureUnit extends MeasureUnit
{

    public function normalizedFactor(): Decimal;

    /**
     * @return NormalizedMeasureUnitComponent[] Sorted by component abbrev
     */
    public function normalizedComponents(): array;
}
