<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

final class StaticMeasureUnitComponent implements MeasureUnitComponent
{

    /**
     * @var Decimal
     */
    private $factor;
    /**
     * @var MetricPrefix
     */
    private $metricPrefix;
    /**
     * @var string
     */
    private $abbrev;
    /**
     * @var int
     */
    private $exponent;

    public function __construct(Decimal $factor, MetricPrefix $metricPrefix, string $abbrev, int $exponent)
    {
        $this->factor = $factor;
        $this->metricPrefix = $metricPrefix;
        $this->abbrev = $abbrev;
        $this->exponent = $exponent;
    }

    public function factor(): Decimal
    {
        return $this->factor;
    }

    public function metricPrefix(): MetricPrefix
    {
        return $this->metricPrefix;
    }

    public function abbrev(): string
    {
        return $this->abbrev;
    }

    public function exponent(): int
    {
        return $this->exponent;
    }

    public function __toString(): string
    {
        $str = '';

        if (!$this->factor->equalsTo(1)) {
            $str .= $this->factor . ' ';
        }

        if (MetricPrefix::NONE() !== $this->metricPrefix) {
            $str .= $this->metricPrefix->symbol();
        }

        $str .= $this->abbrev;

        if (1 === $this->exponent) {
            return $str;
        }

        return $str . $this->exponent;
    }
}
