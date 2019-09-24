<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use LogicException;

final class StaticQuantity implements Quantity
{

    private const ARGS_EXCEPTION_MSG = 'Overloaded arguments mismatch. Consult PHPDoc for more info';

    /**
     * @var float
     */
    private $value;
    /**
     * @var MeasureUnit
     */
    private $measureUnit;
    /**
     * @var Runtime
     */
    private $runtime;

    public function __construct(float $value, MeasureUnit $measureUnit, Runtime $runtime)
    {
        $this->value = $value;
        $this->measureUnit = $measureUnit;
        $this->runtime = $runtime;
    }

    public function apply(callable $callable, ...$args): Quantity
    {
        return new StaticQuantity(
            call_user_func($callable, $this->value, ...$args),
            $this->measureUnit,
            $this->runtime
        );
    }

    /**
     * @inheritDoc
     */
    public function add($valueOrQuantity, $measureUnit = null): Quantity
    {
        /* @var MeasureUnit $unit */
        [$value, $unit] = $this->normalizeOverloadedArgs($valueOrQuantity, $measureUnit);

        return new StaticQuantity(
            $this->value + $unit->in($this->measureUnit)->applyTo($value),
            $this->measureUnit,
            $this->runtime
        );
    }

    /**
     * @inheritDoc
     */
    public function subtract($valueOrQuantity, $measureUnit = null): Quantity
    {
        /* @var MeasureUnit $unit */
        [$value, $unit] = $this->normalizeOverloadedArgs($valueOrQuantity, $measureUnit);

        return new StaticQuantity(
            $this->value - $unit->in($this->measureUnit)->applyTo($value),
            $this->measureUnit,
            $this->runtime
        );
    }

    /**
     * @inheritDoc
     */
    public function multiplyBy($valueOrQuantity, $measureUnit = null): Quantity
    {
        /* @var MeasureUnit $unit */
        [$value, $unit] = $this->normalizeOverloadedArgs($valueOrQuantity, $measureUnit);

        return new StaticQuantity(
            $this->value * $value, $this->measureUnit->multiplyBy($unit), $this->runtime
        );
    }

    /**
     * @inheritDoc
     */
    public function divideBy($valueOrQuantity, $measureUnit = null): Quantity
    {
        /* @var MeasureUnit $unit */
        [$value, $unit] = $this->normalizeOverloadedArgs($valueOrQuantity, $measureUnit);

        if (0. === $value) {
            throw new DivisionByZeroException();
        }

        return new StaticQuantity(
            $this->value / $value, $this->measureUnit->divideBy($unit), $this->runtime
        );
    }

    private function normalizeOverloadedArgs($valueOrQuantity, $measureUnit): array
    {
        if (null === $measureUnit) {
            if (!$valueOrQuantity instanceof Quantity) {
                throw new LogicException(self::ARGS_EXCEPTION_MSG);
            }

            return [$valueOrQuantity->value(), $valueOrQuantity->measureUnit()];
        }

        if (!is_int($valueOrQuantity) && !is_float($valueOrQuantity)) {
            throw new LogicException(self::ARGS_EXCEPTION_MSG);
        }

        if (is_string($measureUnit)) {
            $measureUnit = $this->runtime->parse($measureUnit);
        }

        if (!$measureUnit instanceof MeasureUnit) {
            throw new LogicException(self::ARGS_EXCEPTION_MSG);
        }

        return [(float) $valueOrQuantity, $measureUnit];
    }

    public function invert(): Quantity
    {
        if (0. === $this->value) {
            throw new DivisionByZeroException();
        }

        return new StaticQuantity(1 / $this->value, $this->measureUnit->invert(), $this->runtime);
    }

    public function value(): float
    {
        return $this->value;
    }

    public function measureUnit(): MeasureUnit
    {
        return $this->measureUnit;
    }

    /**
     * @inheritDoc
     */
    public function in($unit): Quantity
    {
        if (is_string($unit)) {
            $unit = $this->runtime->parse($unit);
        }

        if (!$unit instanceof MeasureUnit) {
            throw new LogicException();
        }

        $ratio = $this->measureUnit->in($unit);

        return new StaticQuantity($ratio->applyTo($this->value), $unit, $this->runtime);
    }

    public function __toString(): string
    {
        return $this->value . ' [' . $this->measureUnit . ']';
    }

    /**
     * @inheritDoc
     */
    public function equalsTo($valueOrQuantity, $measureUnitOrEps = null, float $eps = 1e-9): bool
    {
        [$value, $unit] = $this->normalizeOverloadedArgs($valueOrQuantity, $measureUnitOrEps);

        if ($valueOrQuantity instanceof Quantity) {
            if ($measureUnitOrEps === null) {
                $eps = 1e-9;
            } else if (!is_float($eps)) {
                throw new LogicException(self::ARGS_EXCEPTION_MSG);
            } else {
                $eps = $measureUnitOrEps;
            }
        }

        return abs($value - $this->measureUnit->in($unit)->applyTo($this->value)) < $eps;
    }
}
