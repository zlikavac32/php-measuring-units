<?php

declare(strict_types=1);

namespace spec\Zlikavac32\UnitsOfMeasure;

use LogicException;
use PhpSpec\ObjectBehavior;
use Zlikavac32\UnitsOfMeasure\Decimal;
use Zlikavac32\UnitsOfMeasure\MetricPrefix;
use Zlikavac32\UnitsOfMeasure\ParseException;
use Zlikavac32\UnitsOfMeasure\SiFormParser;
use Zlikavac32\UnitsOfMeasure\StaticMeasureUnitComponent;

class SiFormParserSpec extends ObjectBehavior
{

    public function let(): void
    {
        $this->beConstructedWith(['m'], []);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SiFormParser::class);
    }

    public function it_should_throw_exception_when_no_si_units_are_provided(): void
    {
        $this->beConstructedWith([], []);

        $this->shouldThrow(LogicException::class)->duringInstantiation();
    }

    public function it_should_parse_simple_si_unit(): void
    {
        $this->parse('m')->shouldBeLike(
            [
                new StaticMeasureUnitComponent(Decimal::ONE(), MetricPrefix::NONE(), 'm', 1),
            ]
        );
    }

    public function it_should_parse_simple_si_unit_with_pow(): void
    {
        $this->parse('m2')->shouldBeLike(
            [
                new StaticMeasureUnitComponent(Decimal::ONE(), MetricPrefix::NONE(), 'm', 2),
            ]
        );
        $this->parse('m-2')->shouldBeLike(
            [
                new StaticMeasureUnitComponent(Decimal::ONE(), MetricPrefix::NONE(), 'm', -2),
            ]
        );
    }

    public function it_should_parse_simple_si_unit_with_factor(): void
    {
        $this->parse('10 m')->shouldBeLike(
            [
                new StaticMeasureUnitComponent(new Decimal(10), MetricPrefix::NONE(), 'm', 1),
            ]
        );
    }

    public function it_should_parse_simple_si_unit_with_metric_prefix(): void
    {
        $this->parse('km')->shouldBeLike(
            [
                new StaticMeasureUnitComponent(Decimal::ONE(), MetricPrefix::KILO(), 'm', 1),
            ]
        );
    }

    public function it_should_parse_composite_si_unit(): void
    {
        $this->parse('10 km.dm.m-3')->shouldBeLike(
            [
                new StaticMeasureUnitComponent(new Decimal(10), MetricPrefix::KILO(), 'm', 1),
                new StaticMeasureUnitComponent(Decimal::ONE(), MetricPrefix::DECI(), 'm', 1),
                new StaticMeasureUnitComponent(Decimal::ONE(), MetricPrefix::NONE(), 'm', -3),
            ]
        );
    }

    public function it_should_respect_unit_prefix(): void
    {
        $this->beConstructedWith(['kWh', 'Wh'], []);

        $this->parse('kWh')->shouldBeLike(
            [
                new StaticMeasureUnitComponent(Decimal::ONE(), MetricPrefix::NONE(), 'kWh', 1),
            ]
        );
    }

    public function it_should_throw_exception_for_invalid_unit(): void
    {
        $this->shouldThrow(ParseException::class)->duringParse('10 10 m');
        $this->shouldThrow(ParseException::class)->duringParse('ab m');
        $this->shouldThrow(ParseException::class)->duringParse('10 no-unit');
    }
}
