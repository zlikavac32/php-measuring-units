<?php

declare(strict_types=1);

namespace spec\Zlikavac32\UnitsOfMeasure;

use LogicException;
use PhpSpec\ObjectBehavior;
use Zlikavac32\UnitsOfMeasure\Decimal;
use Zlikavac32\UnitsOfMeasure\MeasureUnit;
use Zlikavac32\UnitsOfMeasure\MeasureUnitComponent;
use Zlikavac32\UnitsOfMeasure\MetricPrefix;
use Zlikavac32\UnitsOfMeasure\NormalizedMeasureUnit;
use Zlikavac32\UnitsOfMeasure\NormalizedMeasureUnitComponent;
use Zlikavac32\UnitsOfMeasure\Runtime;
use Zlikavac32\UnitsOfMeasure\StaticMeasureUnit;
use Zlikavac32\UnitsOfMeasure\StaticMeasureUnitComponent;
use Zlikavac32\UnitsOfMeasure\StaticNormalizedMeasureUnitComponent;
use Zlikavac32\UnitsOfMeasure\StaticRatio;

class StaticMeasureUnitSpec extends ObjectBehavior
{

    public function let(Runtime $runtime): void
    {
        $this->beConstructedWith(Decimal::ONE(), [], Decimal::ONE(), [], $runtime);
    }

    public function it_is_initializable(Runtime $runtime): void
    {
        $this->shouldHaveType(StaticMeasureUnit::class);
    }

    public function it_should_invert_unit(
        MeasureUnitComponent $component, NormalizedMeasureUnitComponent $normalizedComponent, Runtime $runtime
    ): void {
        $this->beConstructedWith(new Decimal(2), [$component], new Decimal(4), [$normalizedComponent], $runtime);

        $component->factor()->willReturn(new Decimal(6));
        $component->exponent()->willReturn(4);
        $component->metricPrefix()->willReturn(MetricPrefix::NONE());
        $component->abbrev()->willReturn('comp');

        $normalizedComponent->abbrev()->willReturn('norm');
        $normalizedComponent->exponent()->willReturn(2);

        $this->invert()->shouldBeLike(
            new StaticMeasureUnit(
                new Decimal(1 / 2),
                [
                    new StaticMeasureUnitComponent(
                        new Decimal(6), MetricPrefix::NONE(), 'comp', -4
                    ),
                ],
                new Decimal(1 / 4),
                [
                    new StaticNormalizedMeasureUnitComponent('norm', -2),
                ],
                $runtime->getWrappedObject()
            )
        );
    }

    public function it_should_multiply_with_another_measure_unit(
        NormalizedMeasureUnit $measureUnit,
        MeasureUnitComponent $myComponent,
        NormalizedMeasureUnitComponent $myNormalizedComponent,
        MeasureUnitComponent $otherComponent,
        NormalizedMeasureUnitComponent $otherNormalizedComponent,
        Runtime $runtime
    ): void {
        $this->beConstructedWith(new Decimal(5), [$myComponent], new Decimal(4), [$myNormalizedComponent], $runtime);

        $measureUnit->factor()->willReturn(new Decimal(4));
        $measureUnit->components()->willReturn([$otherComponent]);
        $measureUnit->normalizedFactor()->willReturn(new Decimal(10));
        $measureUnit->normalizedComponents()->willReturn([$otherNormalizedComponent]);

        $myComponent->factor()->willReturn(new Decimal(10));
        $myComponent->metricPrefix()->willReturn(MetricPrefix::KILO());
        $myComponent->abbrev()->willReturn('g');
        $myComponent->exponent()->willReturn(1);

        $otherComponent->factor()->willReturn(new Decimal(10));
        $otherComponent->metricPrefix()->willReturn(MetricPrefix::KILO());
        $otherComponent->abbrev()->willReturn('g');
        $otherComponent->exponent()->willReturn(2);

        $myNormalizedComponent->abbrev()->willReturn('g');
        $myNormalizedComponent->exponent()->willReturn(1);

        $otherNormalizedComponent->abbrev()->willReturn('g');
        $otherNormalizedComponent->exponent()->willReturn(2);

        $this->multiplyBy($measureUnit)->shouldBeLike(
            new StaticMeasureUnit(
                new Decimal(20),
                [
                    new StaticMeasureUnitComponent(new Decimal(10), MetricPrefix::KILO(), 'g', 3),
                ],
                new Decimal(40),
                [
                    new StaticNormalizedMeasureUnitComponent('g', 3),
                ],
                $runtime->getWrappedObject()
            )
        );
    }

    public function it_should_multiply_with_another_measure_unit_as_string(
        NormalizedMeasureUnit $measureUnit,
        MeasureUnitComponent $myComponent,
        NormalizedMeasureUnitComponent $myNormalizedComponent,
        MeasureUnitComponent $otherComponent,
        NormalizedMeasureUnitComponent $otherNormalizedComponent,
        Runtime $runtime
    ): void {
        $this->beConstructedWith(new Decimal(5), [$myComponent], new Decimal(4), [$myNormalizedComponent], $runtime);

        $measureUnit->factor()->willReturn(new Decimal(4));
        $measureUnit->components()->willReturn([$otherComponent]);
        $measureUnit->normalizedFactor()->willReturn(new Decimal(10));
        $measureUnit->normalizedComponents()->willReturn([$otherNormalizedComponent]);

        $myComponent->factor()->willReturn(new Decimal(10));
        $myComponent->metricPrefix()->willReturn(MetricPrefix::KILO());
        $myComponent->abbrev()->willReturn('g');
        $myComponent->exponent()->willReturn(1);

        $otherComponent->factor()->willReturn(new Decimal(10));
        $otherComponent->metricPrefix()->willReturn(MetricPrefix::KILO());
        $otherComponent->abbrev()->willReturn('g');
        $otherComponent->exponent()->willReturn(-1);

        $myNormalizedComponent->abbrev()->willReturn('g');
        $myNormalizedComponent->exponent()->willReturn(1);

        $otherNormalizedComponent->abbrev()->willReturn('g');
        $otherNormalizedComponent->exponent()->willReturn(-1);

        $runtime->parse('unit')->willReturn($measureUnit);

        $this->multiplyBy('unit')->shouldBeLike(
            new StaticMeasureUnit(
                new Decimal(20),
                [

                ],
                new Decimal(40),
                [
                ], $runtime->getWrappedObject()
            )
        );
    }

    public function it_should_throw_exception_when_measure_unit_not_normalized(MeasureUnit $measureUnit): void
    {
        $this->shouldThrow(LogicException::class)->duringMultiplyBy($measureUnit);
    }

    public function it_should_divide_with_another_measure_unit(
        NormalizedMeasureUnit $measureUnit,
        NormalizedMeasureUnit $invertedUnit,
        MeasureUnitComponent $myComponent,
        NormalizedMeasureUnitComponent $myNormalizedComponent,
        MeasureUnitComponent $otherComponent,
        NormalizedMeasureUnitComponent $otherNormalizedComponent,
        Runtime $runtime
    ): void {
        $this->beConstructedWith(new Decimal(5), [$myComponent], new Decimal(4), [$myNormalizedComponent], $runtime);

        $invertedUnit->factor()->willReturn(new Decimal(4));
        $invertedUnit->components()->willReturn([$otherComponent]);
        $invertedUnit->normalizedFactor()->willReturn(new Decimal(10));
        $invertedUnit->normalizedComponents()->willReturn([$otherNormalizedComponent]);

        $myComponent->factor()->willReturn(new Decimal(10));
        $myComponent->metricPrefix()->willReturn(MetricPrefix::KILO());
        $myComponent->abbrev()->willReturn('g');
        $myComponent->exponent()->willReturn(1);

        $otherComponent->factor()->willReturn(new Decimal(10));
        $otherComponent->metricPrefix()->willReturn(MetricPrefix::KILO());
        $otherComponent->abbrev()->willReturn('g');
        $otherComponent->exponent()->willReturn(2);

        $myNormalizedComponent->abbrev()->willReturn('g');
        $myNormalizedComponent->exponent()->willReturn(1);

        $otherNormalizedComponent->abbrev()->willReturn('g');
        $otherNormalizedComponent->exponent()->willReturn(2);

        $measureUnit->invert()->willReturn($invertedUnit);

        $this->multiplyBy($invertedUnit)->shouldBeLike(
            new StaticMeasureUnit(
                new Decimal(20),
                [
                    new StaticMeasureUnitComponent(new Decimal(10), MetricPrefix::KILO(), 'g', 3),
                ],
                new Decimal(40),
                [
                    new StaticNormalizedMeasureUnitComponent('g', 3),
                ],
                $runtime->getWrappedObject()
            )
        );
    }

    public function it_should_convert_itself_in_ratio_of_one(): void
    {
        $this->in($this)->shouldBeLike(new StaticRatio(Decimal::ONE(), false));
    }
}
