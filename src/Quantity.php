<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

interface Quantity
{

    public function apply(callable $callable, ...$args): Quantity;

    /**
     * @param float|int|Quantity $valueOrQuantity
     * @param string|MeasureUnit $measureUnit
     */
    public function add($valueOrQuantity, $measureUnit = null): Quantity;

    /**
     * @param float|int|Quantity $valueOrQuantity
     * @param string|MeasureUnit $measureUnit
     */
    public function subtract($valueOrQuantity, $measureUnit = null): Quantity;

    /**
     * @param float|int|Quantity $valueOrQuantity
     * @param string|MeasureUnit $measureUnit
     */
    public function multiplyBy($valueOrQuantity, $measureUnit = null): Quantity;

    /**
     * @param float|int|Quantity $valueOrQuantity
     * @param string|MeasureUnit $measureUnit
     */
    public function divideBy($valueOrQuantity, $measureUnit = null): Quantity;

    public function invert(): Quantity;

    public function value(): float;

    public function measureUnit(): MeasureUnit;

    public function __toString(): string;

    /**
     * @param MeasureUnit|string $unit
     */
    public function in($unit): Quantity;

    /**
     * @param float|int|Quantity $valueOrQuantity
     * @param string|MeasureUnit|float $measureUnitOrEps
     */
    public function equalsTo($valueOrQuantity, $measureUnitOrEps = null, float $eps = 1e-9): bool;
}
