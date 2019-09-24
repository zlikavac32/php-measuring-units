<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use LogicException;

class DivisionByZeroException extends LogicException
{

    public function __construct()
    {
        parent::__construct('Division by zero');
    }
}
