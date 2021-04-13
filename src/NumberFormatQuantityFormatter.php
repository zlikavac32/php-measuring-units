<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

class NumberFormatQuantityFormatter implements QuantityFormatter
{

    public function __construct(
        private MeasureUnitFormatter $unitFormatter,
        private int $decimals = 2,
        private string $decimalPoint = '.',
        private string $thousandSeparator = ','
    ) { }

    public function format(Quantity $quantity): string
    {
        return number_format($quantity->value(), $this->decimals, $this->decimalPoint, $this->thousandSeparator) . ' '
               . $this->unitFormatter->format($quantity->measureUnit());
    }
}
