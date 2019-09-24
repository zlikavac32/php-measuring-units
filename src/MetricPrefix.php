<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use LogicException;
use Zlikavac32\Enum\Enum;

/**
 * @method static MetricPrefix YOTTA
 * @method static MetricPrefix ZETTA
 * @method static MetricPrefix EXA
 * @method static MetricPrefix PETA
 * @method static MetricPrefix TERA
 * @method static MetricPrefix GIGA
 * @method static MetricPrefix MEGA
 * @method static MetricPrefix KILO
 * @method static MetricPrefix HECTO
 * @method static MetricPrefix DECA
 * @method static MetricPrefix DECI
 * @method static MetricPrefix CENTI
 * @method static MetricPrefix NONE
 * @method static MetricPrefix MILLI
 * @method static MetricPrefix MICRO
 * @method static MetricPrefix NANO
 * @method static MetricPrefix PICO
 * @method static MetricPrefix FEMTO
 * @method static MetricPrefix ATTO
 * @method static MetricPrefix ZEPTO
 * @method static MetricPrefix YOCTO
 */
abstract class MetricPrefix extends Enum
{

    /**
     * @var int
     */
    private $exponent;
    /**
     * @var string
     */
    private $symbol;
    /**
     * @var MetricPrefix[]
     */
    private static $symbolToElementMap = [];
    /**
     * @var Decimal
     */
    private $normalizedFactor;

    public function __construct(string $symbol, int $exponent)
    {
        parent::__construct();

        $this->exponent = $exponent;
        $this->symbol = $symbol;

        self::$symbolToElementMap[$symbol] = $this;

        $this->normalizedFactor = new Decimal(1, $this->exponent);
    }

    public function symbol(): string
    {
        return $this->symbol;
    }

    public function exponent(): int
    {
        return $this->exponent;
    }

    public function asFloat(): float
    {
        return pow(10, $this->exponent);
    }

    public function asDecimal(): Decimal
    {
        return new Decimal(.1, $this->exponent + 1);
    }

    public function to(MetricPrefix $metricPrefix): Decimal
    {
        if ($this === $metricPrefix) {
            return Decimal::ONE();
        } else if ($metricPrefix === MetricPrefix::NONE()) {
            return $this->normalizedFactor;
        }

        return new Decimal(1, $this->exponent - $metricPrefix->exponent);
    }

    public function normalizedFactor(): Decimal
    {
        return $this->normalizedFactor;
    }

    public static function valueOfSymbol(string $symbol): MetricPrefix
    {
        if (!isset(self::$symbolToElementMap[$symbol])) {
            throw new LogicException();
        }

        return self::$symbolToElementMap[$symbol];
    }

    protected static function enumerate(): array
    {
        return [
            'YOTTA' => new class('Y', 24) extends MetricPrefix
            {

            },
            'ZETTA' => new class('Z', 21) extends MetricPrefix
            {

            },
            'EXA' => new class('E', 18) extends MetricPrefix
            {

            },
            'PETA' => new class('P', 15) extends MetricPrefix
            {

            },
            'TERA' => new class('T', 12) extends MetricPrefix
            {

            },
            'GIGA' => new class('G', 9) extends MetricPrefix
            {

            },
            'MEGA' => new class('M', 6) extends MetricPrefix
            {

            },
            'KILO' => new class('k', 3) extends MetricPrefix
            {

            },
            'HECTO' => new class('h', 2) extends MetricPrefix
            {

            },
            'DECA' => new class('da', 1) extends MetricPrefix
            {

            },
            'NONE' => new class('', 0) extends MetricPrefix
            {

            },
            'DECI' => new class('d', -1) extends MetricPrefix
            {

            },
            'CENTI' => new class('c', -2) extends MetricPrefix
            {

            },
            'MILLI' => new class('m', -3) extends MetricPrefix
            {

            },
            'MICRO' => new class('u', -6) extends MetricPrefix
            {

            },
            'NANO' => new class('n', -9) extends MetricPrefix
            {

            },
            'PICO' => new class('p', -12) extends MetricPrefix
            {

            },
            'FEMTO' => new class('f', -15) extends MetricPrefix
            {

            },
            'ATTO' => new class('a', -18) extends MetricPrefix
            {

            },
            'ZEPTO' => new class('z', -21) extends MetricPrefix
            {

            },
            'YOCTO' => new class('y', -24) extends MetricPrefix
            {

            },
        ];
    }
}
