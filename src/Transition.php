<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

final class Transition
{
    /**
     * @var TransitionUnit[]
     */
    private array $transitionUnits;

    public function __construct(private Decimal $ratio, TransitionUnit ...$transitionUnits)
    {
        $this->transitionUnits = $transitionUnits;
    }

    public function ratio(): Decimal
    {
        return $this->ratio;
    }

    /**
     * @return TransitionUnit[]
     */
    public function transitionUnits(): array
    {
        return $this->transitionUnits;
    }
}
