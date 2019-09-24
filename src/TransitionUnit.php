<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

final class TransitionUnit
{

    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $exponent;
    /**
     * @var MetricPrefix
     */
    private $metricPrefix;

    public function __construct(string $name, int $exponent = 1, ?MetricPrefix $metricPrefix = null)
    {
        $this->name = $name;
        $this->exponent = $exponent;
        $this->metricPrefix = $metricPrefix ?? MetricPrefix::NONE();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function metricPrefix(): MetricPrefix
    {
        return $this->metricPrefix;
    }

    public function exponent(): int
    {
        return $this->exponent;
    }
}
