<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use RuntimeException;
use Throwable;

class ConversionException extends RuntimeException
{

    /**
     * @var MeasureUnit
     */
    private $from;
    /**
     * @var MeasureUnit
     */
    private $to;

    public function __construct(MeasureUnit $from, MeasureUnit $to, Throwable $previous = null)
    {
        parent::__construct(sprintf('Unable to convert from %s to %s', $from, $to), 0, $previous);
        $this->from = $from;
        $this->to = $to;
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
