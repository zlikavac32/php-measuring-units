<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use RuntimeException;
use Throwable;

class ParseException extends RuntimeException
{

    public function __construct(private string $measureUnit, Throwable $previous = null)
    {
        parent::__construct(sprintf('Failed to parse %s', $measureUnit), 0, $previous);
    }

    public function measureUnit(): string
    {
        return $this->measureUnit;
    }
}
