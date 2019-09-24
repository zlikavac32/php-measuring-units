<?php

declare(strict_types=1);

namespace spec\Zlikavac32\UnitsOfMeasure;

use PhpSpec\ObjectBehavior;
use Zlikavac32\UnitsOfMeasure\MetricPrefix;
use Zlikavac32\UnitsOfMeasure\TransitionUnit;

class TransitionUnitSpec extends ObjectBehavior
{

    public function let(): void
    {
        $this->beConstructedWith('m');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TransitionUnit::class);
    }

    public function it_should_have_none_as_default_metric_prefix(): void
    {
        $this->metricPrefix()->shouldReturn(MetricPrefix::NONE());
    }

    public function it_should_have_default_exponent_as_one(): void
    {
        $this->exponent()->shouldReturn(1);
    }

    public function it_should_have_meters_as_name(): void
    {
        $this->name()->shouldReturn('m');
    }

    public function it_should_have_custom_configuration(): void
    {
        $this->beConstructedWith('m', 2, MetricPrefix::KILO());

        $this->exponent()->shouldReturn(2);
        $this->metricPrefix()->shouldReturn(MetricPrefix::KILO());
    }
}
