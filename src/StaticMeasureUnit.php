<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use LogicException;

final class StaticMeasureUnit implements NormalizedMeasureUnit
{

    private Decimal $factor;
    /**
     * @var MeasureUnitComponent[]
     */
    private array $components;

    private Decimal $normalizedFactor;
    /**
     * @var NormalizedMeasureUnitComponent[]
     */
    private array $normalizedComponents;
    /**
     * @var NormalizedMeasureUnitComponent[]
     */
    private array $normalizedComponentsMap;

    private Runtime $runtime;

    private Ratio $ratioOfOne;

    /**
     * @param MeasureUnitComponent[] $components
     * @param NormalizedMeasureUnitComponent[] $normalizedComponents
     */
    public function __construct(
        Decimal $factor, array $components, Decimal $normalizedFactor, array $normalizedComponents, Runtime $runtime
    ) {
        $this->factor = $factor;
        $this->components = $components;
        $this->normalizedFactor = $normalizedFactor;
        $this->normalizedComponents = $normalizedComponents;

        $this->normalizedComponentsMap = $this->normalizedComponentsToMap($normalizedComponents);

        $this->runtime = $runtime;
        $this->ratioOfOne = StaticRatio::ONE();
    }

    /**
     * @param NormalizedMeasureUnitComponent[] $normalizedComponents
     *
     * @return NormalizedMeasureUnitComponent[]
     */
    private function normalizedComponentsToMap(array $normalizedComponents): array
    {
        $map = [];

        foreach ($normalizedComponents as $normalizedComponent) {
            if (isset($this->normalizedComponentsMap[$normalizedComponent->abbrev()])) {
                throw new LogicException(sprintf('Component %s already defined', $normalizedComponent->abbrev()));
            }

            $map[$normalizedComponent->abbrev()] = $normalizedComponent;
        }

        ksort($map);

        return $map;
    }

    /**
     * @inheritDoc
     */
    public function multiplyBy($measureUnit): MeasureUnit
    {
        if (is_string($measureUnit)) {
            $measureUnit = $this->runtime->parse($measureUnit);
        }

        if (!$measureUnit instanceof NormalizedMeasureUnit) {
            throw $this->unitTypeNotSupportedException($measureUnit);
        }

        $normalizedComponents = [];

        foreach ($this->normalizedComponents as $normalizedComponent) {
            $normalizedComponents[$normalizedComponent->abbrev()] = $normalizedComponent->exponent();
        }

        foreach ($measureUnit->normalizedComponents() as $otherNormalizedComponent) {
            $abbrev = $otherNormalizedComponent->abbrev();

            if (!isset($normalizedComponents[$abbrev])) {
                $normalizedComponents[$abbrev] = $otherNormalizedComponent->exponent();

                continue;
            }

            $normalizedComponents[$abbrev] += $otherNormalizedComponent->exponent();
        }

        foreach ($normalizedComponents as $abbrev => $exponent) {
            if (0 === $exponent) {
                unset($normalizedComponents[$abbrev]);

                continue;
            }

            $normalizedComponents[$abbrev] = new StaticNormalizedMeasureUnitComponent($abbrev, $exponent);
        }

        $components = [];
        $i = 0;
        $componentsMap = [];

        foreach ([$this->components, $measureUnit->components()] as $componentsToProcess) {
            foreach ($componentsToProcess as $component) {
                /** @var MeasureUnitComponent $component */
                $abbrev = $component->abbrev();
                $prefix = $component->metricPrefix();
                $factor = $component->factor();

                $hash = $abbrev . '|' . $prefix->name() . '|' . $factor->asFloat();

                if (isset($componentsMap[$hash])) {
                    $components[$componentsMap[$hash]][3] += $component->exponent();

                    continue;
                }

                $components[$i] = [$abbrev, $prefix, $factor, $component->exponent()];
                $componentsMap[$hash] = $i++;
            }
        }

        ksort($normalizedComponents);

        foreach ($components as $k => $component) {
            if (0 === $component[3]) {
                unset($components[$k]);
            }
        }

        $components = array_values($components);

        return new StaticMeasureUnit(
            $this->factor->multiplyBy($measureUnit->factor()),
            array_map(
                function (array $componentParts): MeasureUnitComponent {
                    return new StaticMeasureUnitComponent(
                        $componentParts[2], $componentParts[1], $componentParts[0], $componentParts[3]
                    );
                }, $components
            ),
            $this->normalizedFactor->multiplyBy($measureUnit->normalizedFactor()),
            array_values($normalizedComponents),
            $this->runtime
        );
    }

    /**
     * @inheritDoc
     */
    public function divideBy($measureUnit): MeasureUnit
    {
        if (is_string($measureUnit)) {
            $measureUnit = $this->runtime->parse($measureUnit);
        }

        return $this->multiplyBy($measureUnit->invert());
    }

    public function invert(): MeasureUnit
    {
        return new StaticMeasureUnit(
            $this->factor->inverse(),
            array_map(
                function (MeasureUnitComponent $component): MeasureUnitComponent {
                    return new StaticMeasureUnitComponent(
                        $component->factor(), $component->metricPrefix(), $component->abbrev(), -$component->exponent()
                    );
                }, $this->components
            ),
            $this->normalizedFactor->inverse(),
            array_map(
                function (NormalizedMeasureUnitComponent $component): NormalizedMeasureUnitComponent {
                    return new StaticNormalizedMeasureUnitComponent($component->abbrev(), -$component->exponent());
                }, $this->normalizedComponents
            ),
            $this->runtime
        );
    }

    /**
     * @inheritDoc
     */
    public function in($measureUnit): Ratio
    {
        if ($this === $measureUnit) {
            return $this->ratioOfOne;
        }

        if (is_string($measureUnit)) {
            $measureUnit = $this->runtime->parse($measureUnit);
        }

        if (!$measureUnit instanceof NormalizedMeasureUnit) {
            throw $this->unitTypeNotSupportedException($measureUnit);
        }

        if ($measureUnit instanceof StaticMeasureUnit) {
            $otherNormalizedComponentsMap = $measureUnit->normalizedComponentsMap;
        } else {
            $otherNormalizedComponentsMap = $this->normalizedComponentsToMap($measureUnit->normalizedComponents());
        }

        if (count($this->normalizedComponentsMap) !== count($otherNormalizedComponentsMap)) {
            throw new ConversionException($this, $measureUnit);
        }

        $matches = true;
        $matchesInverted = true;

        foreach ($this->normalizedComponentsMap as $abbrev => $component) {
            if (!isset($otherNormalizedComponentsMap[$abbrev])) {
                throw new ConversionException($this, $measureUnit);
            }

            $exponent = $otherNormalizedComponentsMap[$abbrev]->exponent();

            $matches = $matches && $exponent === $component->exponent();
            $matchesInverted = $matchesInverted && $exponent === -$component->exponent();

            if (!$matches && !$matchesInverted) {
                throw new ConversionException($this, $measureUnit);
            }
        }

        if ($matches) {
            return new StaticRatio(
                $this->normalizedFactor->divideBy($measureUnit->normalizedFactor), false
            );
        }

        return new StaticRatio(
            $this->normalizedFactor->multiplyBy($measureUnit->normalizedFactor), true
        );
    }

    private function unitTypeNotSupportedException(MeasureUnit $measureUnit): LogicException
    {
        return new LogicException(
            sprintf(
                'Only instances of %s are supported, but got %s', NormalizedMeasureUnit::class, get_class($measureUnit)
            )
        );
    }

    public function factor(): Decimal
    {
        return $this->factor;
    }

    /**
     * @inheritDoc
     */
    public function components(): array
    {
        return $this->components;
    }

    public function normalizedFactor(): Decimal
    {
        return $this->normalizedFactor;
    }

    /**
     * @inheritDoc
     */
    public function normalizedComponents(): array
    {
        return $this->normalizedComponents;
    }

    public function __toString(): string
    {
        if (empty($this->components)) {
            return (string) $this->factor;
        }

        if ($this->factor->equalsTo(1)) {
            return implode('.', $this->components);
        }

        return $this->factor . ' ' . implode('.', $this->components);
    }

    /**
     * If units represent the same dimension (dm3 is same as one liter)
     *
     * @param string|MeasureUnit $measureUnit
     */
    public function equalsTo($measureUnit, float $eps = 1e-15): bool
    {
        if (is_string($measureUnit)) {
            $measureUnit = $this->runtime->parse($measureUnit);
        }

        if (!$measureUnit instanceof NormalizedMeasureUnit) {
            throw $this->unitTypeNotSupportedException($measureUnit);
        }

        if (!$this->normalizedFactor->equalsTo($measureUnit->normalizedFactor(), $eps)) {
            return false;
        }

        return $this->normalizedComponents == $measureUnit->normalizedComponents();
    }
}
