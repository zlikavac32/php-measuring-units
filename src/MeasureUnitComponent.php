<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

interface MeasureUnitComponent
{

    public function factor(): Decimal;

    public function metricPrefix(): MetricPrefix;

    public function abbrev(): string;

    public function exponent(): int;
}
