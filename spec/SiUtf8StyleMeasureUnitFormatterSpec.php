<?php

declare(strict_types=1);

namespace spec\Zlikavac32\UnitsOfMeasure;

use PhpSpec\ObjectBehavior;
use Zlikavac32\UnitsOfMeasure\Decimal;
use Zlikavac32\UnitsOfMeasure\MeasureUnit;
use Zlikavac32\UnitsOfMeasure\MeasureUnitComponent;
use Zlikavac32\UnitsOfMeasure\MetricPrefix;
use Zlikavac32\UnitsOfMeasure\SiUtf8StyleMeasureUnitFormatter;

class SiUtf8StyleMeasureUnitFormatterSpec extends ObjectBehavior
{

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SiUtf8StyleMeasureUnitFormatter::class);
    }

    public function it_should_format_empty_unit(MeasureUnit $measureUnit): void
    {
        $measureUnit->factor()->willReturn(Decimal::ONE());
        $measureUnit->components()->willReturn([]);

        $this->format($measureUnit)->shouldReturn('');
    }

    public function it_should_format_unit_with_just_factor(MeasureUnit $measureUnit): void
    {
        $measureUnit->factor()->willReturn(new Decimal(2));
        $measureUnit->components()->willReturn([]);

        $this->format($measureUnit)->shouldReturn('2');
    }

    public function it_should_format_complex_unit(
        MeasureUnit $measureUnit, MeasureUnitComponent $first, MeasureUnitComponent $second, MeasureUnitComponent $third
    ): void {
        $measureUnit->factor()->willReturn(new Decimal(2));
        $measureUnit->components()->willReturn([$first, $second, $third]);

        $first->factor()->willReturn(Decimal::ONE());
        $first->abbrev()->willReturn('first');
        $first->exponent()->willReturn(1234567890);
        $first->metricPrefix()->willReturn(MetricPrefix::NONE());

        $second->factor()->willReturn(new Decimal(3));
        $second->abbrev()->willReturn('second');
        $second->exponent()->willReturn(-23);
        $second->metricPrefix()->willReturn(MetricPrefix::MICRO());

        $third->factor()->willReturn(Decimal::ONE());
        $third->abbrev()->willReturn('third');
        $third->exponent()->willReturn(1);
        $third->metricPrefix()->willReturn(MetricPrefix::KILO());

        $this->format($measureUnit)->shouldReturn('2 first¹²³⁴⁵⁶⁷⁸⁹⁰⋅3 μsecond⁻²³⋅kthird');
    }
}
