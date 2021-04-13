<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use RuntimeException;
use Throwable;

class NormalizeException extends RuntimeException
{

    public function __construct(private string $unit, Throwable $previous = null)
    {
        parent::__construct(sprintf('Unable to normalize %s', $unit), 0, $previous);
    }

    public function unit(): string
    {
        return $this->unit;
    }
}
