<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use ArrayAccess;

interface Runtime extends ArrayAccess
{

    public function parse(string $measureUnitAsString): MeasureUnit;

    /**
     * @param string|MeasureUnit $measureUnit
     *
     * @return Quantity
     */
    public function __invoke(float $value, $measureUnit): Quantity;

    /**
     * @param string $measureUnitAsString Measure unit textual representation like m3 for volume or m.s-2 for velocity
     */
    public function offsetGet($measureUnitAsString): MeasureUnit;
}
