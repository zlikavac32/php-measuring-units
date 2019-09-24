<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

final class StaticNormalizedMeasureUnitComponent implements NormalizedMeasureUnitComponent
{

    /**
     * @var string
     */
    private $abbrev;
    /**
     * @var int
     */
    private $exponent;

    public function __construct(string $abbrev, int $exponent)
    {
        $this->abbrev = $abbrev;
        $this->exponent = $exponent;
    }

    public function abbrev(): string
    {
        return $this->abbrev;
    }

    public function exponent(): int
    {
        return $this->exponent;
    }
}
