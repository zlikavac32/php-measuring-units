<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use RuntimeException;
use Throwable;

class NormalizeException extends RuntimeException
{

    private string $unit;

    public function __construct(string $unit, Throwable $previous = null)
    {
        parent::__construct(sprintf('Unable to normalize %s', $unit), 0, $previous);
        $this->unit = $unit;
    }

    public function unit(): string
    {
        return $this->unit;
    }
}
