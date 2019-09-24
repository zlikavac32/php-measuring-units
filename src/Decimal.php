<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use LogicException;

/**
 * Stores number in 0.xxxx * 10^y representation
 */
final class Decimal
{

    /**
     * @var float
     */
    private $mantisa;
    /**
     * @var int
     */
    private $exponent;
    /**
     * @var float
     */
    private $asFloat;
    /**
     * @var int
     */
    private $sign;

    public function __construct(float $mantisa, int $exponent = 0)
    {

        [$mantisaNormalized, $exponentNormalized] = $this->normalize($mantisa);

        $this->mantisa = $mantisaNormalized;
        $this->exponent = $exponentNormalized + $exponent;
        $this->asFloat = $this->mantisa * pow(10, $this->exponent);
        $this->sign = sign($mantisaNormalized);
    }

    public function inverse(): Decimal
    {
        if ($this->isOne()) {
            return $this;
        }

        if (0. === $this->mantisa) {
            throw new DivisionByZeroException();
        }

        [$mantisa, $exponent] = $this->normalize(1 / $this->mantisa);

        return new Decimal($mantisa, $exponent - $this->exponent);
    }

    public function multiplyBy(Decimal $decimal): Decimal
    {
        if ($decimal->isOne()) {
            return $this;
        }

        if ($this->isOne()) {
            return $decimal;
        }

        [$mantisa, $exponent] = $this->normalize($this->mantisa * $decimal->mantisa);

        return new Decimal($mantisa, $exponent + $this->exponent + $decimal->exponent);
    }

    public function divideBy(Decimal $decimal): Decimal
    {
        if (0. === $decimal->mantisa) {
            throw new DivisionByZeroException();
        }

        if ($decimal->isOne()) {
            return $this;
        }

        if ($this->isOne()) {
            return $decimal->inverse();
        }

        [$mantisa, $exponent] = $this->normalize($this->mantisa / $decimal->mantisa);

        return new Decimal($mantisa, $exponent + $this->exponent - $decimal->exponent);
    }

    public function pow(int $exp): Decimal
    {
        if ($this->isOne() || 1 === $exp) {
            return $this;
        } else {
            if (0 === $exp) {
                return Decimal::ONE();
            }
        }

        [$mantisa, $exponent] = $this->normalize(pow($this->mantisa, $exp));

        return new Decimal($mantisa, $exponent + $this->exponent * $exp);
    }

    public function asFloat(): float
    {
        return $this->asFloat;
    }

    public function sign(): int
    {
        return $this->sign;
    }

    /**
     * @param float|int|Decimal $decimal
     */
    public function equalsTo($decimal, float $epsilon = 1e-15): bool
    {
        return $this->compareTo($decimal, $epsilon) === 0;
    }

    /**
     * @param int|float|Decimal $decimal
     */
    public function compareTo($decimal, float $epsilon = 1e-15): int
    {
        if (is_float($decimal) || is_int($decimal)) {
            [$mantisa, $exponent] = $this->normalize($decimal);
        } else {
            if ($decimal instanceof Decimal) {
                $mantisa = $decimal->mantisa;
                $exponent = $decimal->exponent;
            } else {
                throw new LogicException();
            }
        }

        $cmp = $this->exponent - $exponent;

        if ($cmp === -1) {
            $mantisa *= 10;
        } else if ($cmp === 1) {
            $mantisa /= 10;
        } else if (0 !== $cmp) {
            return $cmp;
        }

        $diff = $this->mantisa - $mantisa;

        if (abs($diff) < $epsilon) {
            return 0;
        }

        return sign($diff);
    }

    public function __toString(): string
    {
        return sprintf('[%s, %d]', (string) $this->mantisa, $this->exponent);
    }

    public static function ONE(): Decimal
    {
        static $one;

        if (null === $one) {
            $one = new Decimal(1);
        }

        return $one;
    }

    private function normalize(float $value): array
    {
        $exponent = (int) ceil(log10(abs($value)));

        return [$value / pow(10, $exponent), $exponent];
    }

    private function isOne(): bool
    {
        return $this->equalsTo(1);
    }
}
