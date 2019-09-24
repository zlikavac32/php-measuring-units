<?php

declare(strict_types=1);

namespace spec\Zlikavac32\UnitsOfMeasure;

use Ds\Map;
use Ds\Set;
use LogicException;
use PhpSpec\ObjectBehavior;
use Zlikavac32\UnitsOfMeasure\Decimal;
use Zlikavac32\UnitsOfMeasure\MapUnitNormalizer;
use Zlikavac32\UnitsOfMeasure\MetricPrefix;
use Zlikavac32\UnitsOfMeasure\NormalizeException;
use Zlikavac32\UnitsOfMeasure\StaticMeasureUnitComponent;
use Zlikavac32\UnitsOfMeasure\StaticNormalizedMeasureUnitComponent;
use Zlikavac32\UnitsOfMeasure\Transition;
use Zlikavac32\UnitsOfMeasure\TransitionUnit;

class MapUnitNormalizerSpec extends ObjectBehavior
{

    public function it_is_initializable(): void
    {
        $this->beConstructedWith(new Map(), new Set(['m']));

        $this->shouldHaveType(MapUnitNormalizer::class);
    }

    public function it_should_fail_to_construct_when_there_are_no_base_units_defined(): void
    {
        $this->beConstructedWith(new Map(), new Set([]));

        $this->shouldThrow(LogicException::class)->duringInstantiation();
    }

    public function it_should_normalize_base_unit(): void
    {
        $this->beConstructedWith(new Map(), new Set(['m']));

        $this->normalize([new StaticMeasureUnitComponent(new Decimal(2), MetricPrefix::KILO(), 'm', 3)])
             ->shouldBeLike(
                 [
                     new Decimal(2, 3 * 3), [new StaticNormalizedMeasureUnitComponent('m', 3)],
                 ]
             );

        $this->normalize([new StaticMeasureUnitComponent(new Decimal(2), MetricPrefix::KILO(), 'm', -3)])
             ->shouldBeLike(
                 [
                     new Decimal(1 / 2, 3 * -3), [new StaticNormalizedMeasureUnitComponent('m', -3)],
                 ]
             );
    }

    public function it_should_normalize_derived_unit(): void
    {
        $this->beConstructedWith(
            new Map(
                [
                    'C' => new Transition(
                        Decimal::ONE(), new TransitionUnit('s'), new TransitionUnit('A')
                    ),
                ]
            ), new Set(['s', 'A'])
        );

        $this->normalize([new StaticMeasureUnitComponent(new Decimal(2), MetricPrefix::KILO(), 'C', 3)])
             ->shouldBeLike(
                 [
                     new Decimal(2, 3 * 3),
                     [
                         new StaticNormalizedMeasureUnitComponent('A', 3),
                         new StaticNormalizedMeasureUnitComponent('s', 3),
                     ],
                 ]
             );

        $this->normalize([new StaticMeasureUnitComponent(new Decimal(2), MetricPrefix::KILO(), 'C', -3)])
             ->shouldBeLike(
                 [
                     new Decimal(1 / 2, 3 * -3),
                     [
                         new StaticNormalizedMeasureUnitComponent('A', -3),
                         new StaticNormalizedMeasureUnitComponent('s', -3),
                     ],
                 ]
             );
    }

    public function it_should_normalize_recursive_derived_unit(): void
    {
        $this->beConstructedWith(
            new Map(
                [
                    'Pa' => new Transition(
                        Decimal::ONE(), new TransitionUnit('N'), new TransitionUnit('m', -2)
                    ),
                    'N' => new Transition(
                        Decimal::ONE(), new TransitionUnit('g', 1, MetricPrefix::KILO()), new TransitionUnit('m', 1),
                        new TransitionUnit('s', -2)
                    ),
                ]
            ), new Set(['m', 'g', 's'])
        );

        $this->normalize([new StaticMeasureUnitComponent(new Decimal(2), MetricPrefix::KILO(), 'Pa', 3)])
             ->shouldBeLike(
                 [
                     new Decimal(2, 3 * 3 + 3 * 3), [
                     new StaticNormalizedMeasureUnitComponent('g', 3),
                     new StaticNormalizedMeasureUnitComponent('m', -3),
                     new StaticNormalizedMeasureUnitComponent('s', -6),
                 ],
                 ]
             );

        $this->normalize([new StaticMeasureUnitComponent(new Decimal(2), MetricPrefix::KILO(), 'Pa', -3)])
             ->shouldBeLike(
                 [
                     new Decimal(1 / 2, 3 * -3 + 3 * -3), [
                     new StaticNormalizedMeasureUnitComponent('g', -3),
                     new StaticNormalizedMeasureUnitComponent('m', 3), new StaticNormalizedMeasureUnitComponent('s', 6),
                 ],
                 ]
             );
    }

    public function it_should_normalize_composite_derived_unit(): void
    {
        $this->beConstructedWith(
            new Map(
                [
                    'N' => new Transition(
                        Decimal::ONE(), new TransitionUnit('g', 1, MetricPrefix::KILO()), new TransitionUnit('m', 1),
                        new TransitionUnit('s', -2)
                    ),
                ]
            ), new Set(['m', 'g', 's'])
        );

        $this->normalize(
            [
                new StaticMeasureUnitComponent(new Decimal(2), MetricPrefix::KILO(), 'N', 3),
                new StaticMeasureUnitComponent(new Decimal(5), MetricPrefix::DECI(), 'm', 1),
            ]
        )
             ->shouldBeLike(
                 [
                     new Decimal(2 * 5, 3 * 3 + 3 * 3 - 1), [
                     new StaticNormalizedMeasureUnitComponent('g', 3), new StaticNormalizedMeasureUnitComponent('m', 4),
                     new StaticNormalizedMeasureUnitComponent('s', -6),
                 ],
                 ]
             );

        $this->normalize(
            [
                new StaticMeasureUnitComponent(new Decimal(2), MetricPrefix::KILO(), 'N', -3),
                new StaticMeasureUnitComponent(new Decimal(5), MetricPrefix::DECI(), 'm', 1),
            ]
        )
             ->shouldBeLike(
                 [
                     new Decimal(5 / 2, 3 * -3 + 3 * -3 - 1), [
                     new StaticNormalizedMeasureUnitComponent('g', -3),
                     new StaticNormalizedMeasureUnitComponent('m', -2),
                     new StaticNormalizedMeasureUnitComponent('s', 6),
                 ],
                 ]
             );
    }

    public function it_should_elimiate_those_with_exponent_zero(): void
    {
        $this->beConstructedWith(
            new Map(
                [
                    'C' => new Transition(
                        Decimal::ONE(), new TransitionUnit('s'), new TransitionUnit('A')
                    ),
                ]
            ), new Set(['s', 'A'])
        );

        $this->normalize(
            [
                new StaticMeasureUnitComponent(new Decimal(4), MetricPrefix::NONE(), 'C', 1),
                new StaticMeasureUnitComponent(new Decimal(2), MetricPrefix::NONE(), 's', -1),
            ]
        )
             ->shouldBeLike(
                 [
                     new Decimal(2),
                     [
                         new StaticNormalizedMeasureUnitComponent('A', 1),
                     ],
                 ]
             );
    }

    public function it_should_throw_exception_for_unknown_transition(): void
    {
        $this->beConstructedWith(
            new Map(
                [
                    'Pa' => new Transition(
                        Decimal::ONE(), new TransitionUnit('N'), new TransitionUnit('m', -2)
                    ),
                ]
            ), new Set(['m'])
        );

        $this->shouldThrow(LogicException::class)->duringInstantiation();
    }

    public function it_should_throw_exception_for_unknown_unit_during_normalize(): void
    {
        $this->beConstructedWith(new Map(), new Set(['m']));

        $this->shouldThrow(NormalizeException::class)->duringNormalize(
            [
                new StaticMeasureUnitComponent(Decimal::ONE(), MetricPrefix::NONE(), 'C', 1),
            ]
        );
    }
}
