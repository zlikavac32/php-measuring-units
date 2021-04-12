<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use RuntimeException;
use Throwable;

class ConversionException extends RuntimeException
{

    public function __construct(private MeasureUnit $from, private MeasureUnit $to, Throwable $previous = null)
    {
        parent::__construct(sprintf('Unable to convert from %s to %s', $from, $to), 0, $previous);
    }

    public function from(): MeasureUnit
    {
        return $this->from;
    }

    public function to(): MeasureUnit
    {
        return $this->to;
    }
}
