<?php

declare(strict_types=1);

namespace spec\Zlikavac32\UnitsOfMeasure;

use PhpSpec\ObjectBehavior;
use Zlikavac32\UnitsOfMeasure\Decimal;
use Zlikavac32\UnitsOfMeasure\StaticRatio;

class StaticRatioSpec extends ObjectBehavior
{

    public function let(): void
    {
        $this->beConstructedWith(new Decimal(3.2), false);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(StaticRatio::class);
    }

    public function it_should_apply_ratio_to_value(): void
    {
        $this->applyTo(2.3)->shouldBeApproximately(2.3 * 3.2, 1e-9);
    }

    public function it_should_return_string_representation(): void
    {
        $this->__toString()->shouldReturn('[0.32, 1] no-invert');
    }

    public function it_should_return_string_representation_when_inverted(): void
    {
        $this->beConstructedWith(new Decimal(3.2), true);

        $this->__toString()->shouldReturn('[0.32, 1] invert');
    }

    public function it_should_invert_value(): void
    {
        $this->beConstructedWith(new Decimal(10), true);

        $this->applyTo(2)->shouldBeApproximately(0.05, 1e-9);
    }
}
