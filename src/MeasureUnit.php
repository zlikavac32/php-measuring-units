<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

interface MeasureUnit
{

    /**
     * @param string|MeasureUnit $measureUnit
     */
    public function multiplyBy($measureUnit): MeasureUnit;

    /**
     * @param string|MeasureUnit $measureUnit
     */
    public function divideBy($measureUnit): MeasureUnit;

    public function invert(): MeasureUnit;

    /**
     * @param string|MeasureUnit $measureUnit
     */
    public function in($measureUnit): Ratio;

    public function factor(): Decimal;

    public function __toString(): string;

    /**
     * @return MeasureUnitComponent[]
     */
    public function components(): array;

    /**
     * If units represent the same dimension (dm3 is same as one liter)
     *
     * @param string|MeasureUnit $measureUnit
     */
    public function equalsTo($measureUnit, float $eps = 1e-15): bool;
}
