<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

interface Quantity
{

    public function apply(callable $callable, ...$args): Quantity;

    public function add(float|int|Quantity $valueOrQuantity, string|MeasureUnit|null $measureUnit = null): Quantity;

    public function subtract(float|int|Quantity $valueOrQuantity, string|MeasureUnit|null $measureUnit = null): Quantity;

    public function multiplyBy(float|int|Quantity $valueOrQuantity, string|MeasureUnit|null $measureUnit = null): Quantity;

    public function divideBy(float|int|Quantity $valueOrQuantity, string|MeasureUnit|null $measureUnit = null): Quantity;

    public function invert(): Quantity;

    public function value(): float;

    public function measureUnit(): MeasureUnit;

    public function __toString(): string;

    public function in(MeasureUnit|string $unit): Quantity;

    public function equalsTo(float|int|Quantity $valueOrQuantity, string|MeasureUnit|float|null $measureUnitOrEps = null, float $eps = 1e-9): bool;
}
