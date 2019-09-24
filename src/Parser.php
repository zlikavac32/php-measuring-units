<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

interface Parser
{

    /**
     * @return MeasureUnitComponent[]
     */
    public function parse(string $measureUnit): array;
}
