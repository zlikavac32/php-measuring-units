<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

interface NormalizedMeasureUnitComponent
{

    public function abbrev(): string;

    public function exponent(): int;
}
