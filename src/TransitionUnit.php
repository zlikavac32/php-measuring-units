<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

final class TransitionUnit
{
    private MetricPrefix $metricPrefix;

    public function __construct(
        private string $name,
        private int $exponent = 1,
        ?MetricPrefix $metricPrefix = null
    ) {
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
