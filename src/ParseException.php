<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use RuntimeException;
use Throwable;

class ParseException extends RuntimeException
{

    private string $measureUnit;

    public function __construct(string $measureUnit, Throwable $previous = null)
    {
        parent::__construct(sprintf('Failed to parse %s', $measureUnit), 0, $previous);

        $this->measureUnit = $measureUnit;
    }

    public function measureUnit(): string
    {
        return $this->measureUnit;
    }
}
