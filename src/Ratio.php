<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

interface Ratio
{

    public function applyTo(float $value): float;

    public function ratio(): Decimal;

    public function inverted(): bool;

    public function __toString(): string;
}
