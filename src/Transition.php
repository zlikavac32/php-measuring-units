<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

final class Transition
{

    private Decimal $ratio;
    /**
     * @var TransitionUnit[]
     */
    private array $transitionUnits;

    public function __construct(Decimal $ratio, TransitionUnit ...$transitionUnits)
    {
        $this->ratio = $ratio;
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
