<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

class CachedRuntime implements Runtime
{

    private Runtime $runtime;
    /**
     * @var MeasureUnit[]
     */
    private array $cache = [];

    public function __construct(Runtime $runtime)
    {
        $this->runtime = $runtime;
    }

    public function parse(string $measureUnitAsString): MeasureUnit
    {
        if (isset($this->cache[$measureUnitAsString])) {
            return $this->cache[$measureUnitAsString];
        }

        return $this->cache[$measureUnitAsString] = $this->runtime->parse($measureUnitAsString);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(float $value, $measureUnit): Quantity
    {
        return $this->runtime->__invoke(
            $value, is_string($measureUnit) ? $this->parse($measureUnit) : $measureUnit
        );
    }

    public function clear(): void
    {
        $this->cache = [];
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($measureUnitAsString): MeasureUnit
    {
        return $this->parse($measureUnitAsString);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        throw new MethodNotSupportedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        throw new MethodNotSupportedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        throw new MethodNotSupportedException(__METHOD__);
    }
}
