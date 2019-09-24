<?php

declare(strict_types=1);

namespace spec\Zlikavac32\UnitsOfMeasure;

use PhpSpec\ObjectBehavior;
use Zlikavac32\UnitsOfMeasure\PhpToStringQuantityFormatter;
use Zlikavac32\UnitsOfMeasure\Quantity;

class PhpToStringQuantityFormatterSpec extends ObjectBehavior
{

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PhpToStringQuantityFormatter::class);
    }

    public function it_should_return_to_string_representation(Quantity $quantity): void
    {
        $quantity->__toString()->willReturn('1 unit');

        $this->format($quantity)->shouldReturn('1 unit');
    }
}
