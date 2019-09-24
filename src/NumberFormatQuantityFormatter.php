<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

class NumberFormatQuantityFormatter implements QuantityFormatter
{

    /**
     * @var MeasureUnitFormatter
     */
    private $unitFormatter;
    /**
     * @var int
     */
    private $decimals;
    /**
     * @var string
     */
    private $decimalPoint;
    /**
     * @var string
     */
    private $thousandSeparator;

    public function __construct(
        MeasureUnitFormatter $unitFormatter, int $decimals = 2, string $decimalPoint = '.',
        string $thousandSeparator = ','
    ) {
        $this->unitFormatter = $unitFormatter;
        $this->decimals = $decimals;
        $this->decimalPoint = $decimalPoint;
        $this->thousandSeparator = $thousandSeparator;
    }

    public function format(Quantity $quantity): string
    {
        return number_format($quantity->value(), $this->decimals, $this->decimalPoint, $this->thousandSeparator) . ' '
               . $this->unitFormatter->format($quantity->measureUnit());
    }
}
