<?php

declare(strict_types=1);

namespace spec\Zlikavac32\UnitsOfMeasure;

use PhpSpec\ObjectBehavior;
use Zlikavac32\UnitsOfMeasure\MeasureUnit;
use Zlikavac32\UnitsOfMeasure\PhpToStringMeasureUnitFormatter;

class PhpToStringMeasureUnitFormatterSpec extends ObjectBehavior
{

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PhpToStringMeasureUnitFormatter::class);
    }

    public function it_should_return_to_string_representation(MeasureUnit $measureUnit): void
    {
        $measureUnit->__toString()->willReturn('unit');

        $this->format($measureUnit)->shouldReturn('unit');
    }
}
