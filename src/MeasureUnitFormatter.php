<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

interface MeasureUnitFormatter
{

    public function format(MeasureUnit $measureUnit): string;
}
