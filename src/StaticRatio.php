<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use LogicException;

final class StaticRatio implements Ratio
{

    /**
     * @var Decimal
     */
    private $ratio;
    /**
     * @var bool
     */
    private $invert;

    public function __construct(Decimal $ratio, bool $invert)
    {
        if ($ratio->equalsTo(0)) {
            throw new LogicException();
        }

        $this->ratio = $ratio;
        $this->invert = $invert;
    }

    public function applyTo(float $value): float
    {
        if ($this->invert && 0. === $value) {
            return $value;
        }

        if ($this->ratio->equalsTo(1)) {
            return $this->invert ? 1 / $value : $value;
        }

        $ret = $this->ratio->multiplyBy(new Decimal($value));

        return $this->invert ? $ret->inverse()->asFloat() : $ret->asFloat();
    }

    public function ratio(): Decimal
    {
        return $this->ratio;
    }

    public function inverted(): bool
    {
        return $this->invert;
    }

    public function __toString(): string
    {
        return (string) $this->ratio . ' ' . ['no-invert', 'invert'][$this->invert];
    }

    public static function ONE(): Ratio
    {
        static $one;

        if (null === $one) {
            $one = new StaticRatio(new Decimal(1), false);
        }

        return $one;
    }
}
