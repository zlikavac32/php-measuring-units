<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

interface QuantityFormatter
{

    public function format(Quantity $quantity): string;
}
