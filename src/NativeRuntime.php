<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use LogicException;

final class NativeRuntime implements Runtime
{

    private Parser $parser;

    private Normalizer $normalizer;

    public function __construct(Parser $parser, Normalizer $normalizer)
    {
        $this->parser = $parser;
        $this->normalizer = $normalizer;
    }

    public function parse(string $measureUnitAsString): MeasureUnit
    {
        if (empty($measureUnitAsString)) {
            return new StaticMeasureUnit(Decimal::ONE(), [], Decimal::ONE(), [], $this);
        }

        if (is_numeric($measureUnitAsString)) {
            $factor = new Decimal((float) $measureUnitAsString);

            return new StaticMeasureUnit($factor, [], $factor, [], $this);
        }

        $components = $this->parser->parse($measureUnitAsString);

        [$normalizedFactor, $normalizedComponents] = $this->normalizer->normalize($components);

        return new StaticMeasureUnit(Decimal::ONE(), $components, $normalizedFactor, $normalizedComponents, $this);
    }

    public function offsetExists($offset): bool
    {
        throw new MethodNotSupportedException(__METHOD__);
    }

    public function offsetSet($offset, $value): void
    {
        throw new MethodNotSupportedException(__METHOD__);
    }

    public function offsetUnset($offset): void
    {
        throw new MethodNotSupportedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(float $value, $measureUnit): Quantity
    {
        if (is_string($measureUnit)) {
            $measureUnit = $this->parse($measureUnit);
        }

        if (!$measureUnit instanceof MeasureUnit) {
            throw new LogicException(sprintf('Expected measure unit as string or instance of %s', MeasureUnit::class));
        }

        return new StaticQuantity($value, $measureUnit, $this);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($measureUnitAsString): MeasureUnit
    {
        if (!is_string($measureUnitAsString)) {
            throw new LogicException('Expected string as measure unit');
        }

        return $this->parse($measureUnitAsString);
    }
}
