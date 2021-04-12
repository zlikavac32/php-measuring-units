<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

interface MeasureUnit
{

    public function multiplyBy(string|MeasureUnit $measureUnit): MeasureUnit;

    public function divideBy(string|MeasureUnit $measureUnit): MeasureUnit;

    public function invert(): MeasureUnit;

    public function in(string|MeasureUnit $measureUnit): Ratio;

    public function factor(): Decimal;

    public function __toString(): string;

    /**
     * @return MeasureUnitComponent[]
     */
    public function components(): array;

    /**
     * If units represent the same dimension (dm3 is same as one liter)
     */
    public function equalsTo(string|MeasureUnit $measureUnit, float $eps = 1e-15): bool;
}
