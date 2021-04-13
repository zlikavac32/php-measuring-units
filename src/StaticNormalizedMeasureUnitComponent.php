<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

final class StaticNormalizedMeasureUnitComponent implements NormalizedMeasureUnitComponent
{

    public function __construct(private string $abbrev, private int $exponent)
    { }

    public function abbrev(): string
    {
        return $this->abbrev;
    }

    public function exponent(): int
    {
        return $this->exponent;
    }
}
