<?php

declare(strict_types=1);

namespace spec\Zlikavac32\UnitsOfMeasure;

use PhpSpec\ObjectBehavior;
use Zlikavac32\UnitsOfMeasure\MeasureUnit;
use Zlikavac32\UnitsOfMeasure\MeasureUnitFormatter;
use Zlikavac32\UnitsOfMeasure\NumberFormatQuantityFormatter;
use Zlikavac32\UnitsOfMeasure\Quantity;

class NumberFormatQuantityFormatterSpec extends ObjectBehavior
{

    public function let(MeasureUnitFormatter $unitFormatter): void
    {
        $this->beConstructedWith($unitFormatter);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(NumberFormatQuantityFormatter::class);
    }

    public function it_should_format_quantity(
        MeasureUnitFormatter $unitFormatter, Quantity $quantity, MeasureUnit $measureUnit
    ): void {
        $quantity->value()->willReturn(23000.237);
        $quantity->measureUnit()->willReturn($measureUnit);

        $unitFormatter->format($measureUnit)->willReturn('unit');

        $this->format($quantity)->shouldReturn('23,000.24 unit');
    }

    public function it_should_format_quantity_with_different_args_in_constructor(
        MeasureUnitFormatter $unitFormatter, Quantity $quantity, MeasureUnit $measureUnit
    ): void {
        $this->beConstructedWith($unitFormatter, 3, ',', '.');

        $quantity->value()->willReturn(23000.2378);
        $quantity->measureUnit()->willReturn($measureUnit);

        $unitFormatter->format($measureUnit)->willReturn('unit');

        $this->format($quantity)->shouldReturn('23.000,238 unit');
    }
}
