<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;


use Ds\Map;
use Ds\Set;
use LogicException;

final class MapUnitNormalizer implements Normalizer
{

    /**
     * @var Transition[]
     */
    private Map $mapOfUnits;

    /**
     * @param Map|Transition[] $mapOfUnits
     * @param Set|string[] $baseUnits
     */
    public function __construct(Map $mapOfUnits, private Set $baseUnits)
    {
        if ($baseUnits->count() === 0) {
            throw new LogicException('At least one base unit must be defined');
        }

        $this->mapOfUnits = new Map();

        $this->buildMapOfUnits($mapOfUnits);

        $this->mapOfUnitsArr = $this->mapOfUnits->toArray();
    }

    /**
     * @inheritDoc
     */
    public function normalize(array $components): array
    {
        $ratio = Decimal::ONE();

        $normalizedComponents = [];

        foreach ($components as $component) {
            $abbrev = $component->abbrev();
            $exponent = $component->exponent();
            $exponentSign = sign($component->exponent());

            $ratio = $ratio->multiplyBy($component->factor()->pow($exponentSign))->multiplyBy(
                $component->metricPrefix()->normalizedFactor()->pow($component->exponent())
            );

            if ($this->baseUnits->contains($abbrev)) {
                if (!isset($normalizedComponents[$abbrev])) {
                    $normalizedComponents[$abbrev] = 0;
                }

                $normalizedComponents[$abbrev] += $exponent;

            } else {
                if ($this->mapOfUnits->hasKey($abbrev)) {
                    $transition = $this->mapOfUnits[$abbrev];

                    $ratio = $ratio->multiplyBy($transition->ratio()->pow($exponent));

                    foreach ($transition->transitionUnits() as $unit) {
                        $transitionAbbrev = $unit->name();

                        if (!isset($normalizedComponents[$transitionAbbrev])) {
                            $normalizedComponents[$transitionAbbrev] = 0;
                        }

                        $normalizedComponents[$transitionAbbrev] += $unit->exponent() * $exponent;
                    }
                } else {
                    throw new NormalizeException($abbrev);
                }
            }
        }

        foreach ($normalizedComponents as $abbrev => $exponent) {
            if (0 === $exponent) {
                unset($normalizedComponents[$abbrev]);

                continue;
            }

            $normalizedComponents[$abbrev] = new StaticNormalizedMeasureUnitComponent($abbrev, $exponent);
        }

        usort(
            $normalizedComponents,
            function (NormalizedMeasureUnitComponent $first, NormalizedMeasureUnitComponent $second): int {
                return $first->abbrev() <=> $second->abbrev();
            }
        );

        return [$ratio, array_values($normalizedComponents)];
    }

    /**
     * @param Map|Transition[] $mapOfUnits
     */
    private function buildMapOfUnits(Map $mapOfUnits): void
    {
        $toProcess = new Map();

        foreach ($mapOfUnits as $unit => $transition) {
            foreach ($transition->transitionUnits() as $transitionUnit) {
                if ($this->baseUnits->contains($transitionUnit)) {
                    continue;
                }

                $toProcess->put($unit, $transition);

                continue 2;
            }

            $this->mapOfUnits[$unit] = $transition;
        }

        while (!$toProcess->isEmpty()) {
            $first = $toProcess->first();

            $this->buildMapOfUnitsRecursive($first->key, $toProcess);
        }
    }

    /**
     * @param Map|Transition[] $toProcess
     */
    private function buildMapOfUnitsRecursive(string $name, Map $toProcess): void
    {
        if (!$toProcess->hasKey($name)) {
            return;
        }

        $transition = $toProcess[$name];

        $toProcess->remove($name);

        $ratio = $transition->ratio();
        $transitionUnits = new Map();

        foreach ($transition->transitionUnits() as $transitionUnit) {
            $abbrev = $transitionUnit->name();

            $ratio = $ratio->multiplyBy(
                $transitionUnit->metricPrefix()->normalizedFactor()->pow($transitionUnit->exponent())
            );

            if ($this->baseUnits->contains($abbrev)) {
                if (!$transitionUnits->hasKey($abbrev)) {
                    $transitionUnits->put($abbrev, 0);
                }

                $transitionUnits->put($abbrev, $transitionUnits->get($abbrev) + $transitionUnit->exponent());

                continue;
            } else {
                if ($toProcess->hasKey($abbrev)) {
                    $this->buildMapOfUnitsRecursive($abbrev, $toProcess);
                } else {
                    if (!$this->mapOfUnits->hasKey($abbrev)) {
                        throw new LogicException(sprintf('Unknown transition for %s', $abbrev));
                    }
                }
            }

            $ratio = $ratio->multiplyBy($this->mapOfUnits[$abbrev]->ratio()->pow($transitionUnit->exponent()));

            foreach ($this->mapOfUnits[$abbrev]->transitionUnits() as $transitionUnitToMerge) {
                $abbrevToMerge = $transitionUnitToMerge->name();

                if (!$transitionUnits->hasKey($abbrevToMerge)) {
                    $transitionUnits->put($abbrevToMerge, 0);
                }

                $transitionUnits->put(
                    $abbrevToMerge, $transitionUnits->get($abbrevToMerge) + $transitionUnitToMerge->exponent()
                                                                            * $transitionUnit->exponent()
                );
            }
        }

        $transitionUnitsArray = [];

        foreach ($transitionUnits as $abbrev => $exponent) {
            if (0 === $exponent) {
                continue;
            }

            $transitionUnitsArray[] = new TransitionUnit($abbrev, $exponent);
        }

        $this->mapOfUnits[$name] = new Transition($ratio, ...$transitionUnitsArray);
    }
}
