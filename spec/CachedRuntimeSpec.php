<?php

declare(strict_types=1);

namespace spec\Zlikavac32\UnitsOfMeasure;

use PhpSpec\ObjectBehavior;
use Zlikavac32\UnitsOfMeasure\CachedRuntime;
use Zlikavac32\UnitsOfMeasure\MeasureUnit;
use Zlikavac32\UnitsOfMeasure\MethodNotSupportedException;
use Zlikavac32\UnitsOfMeasure\Quantity;
use Zlikavac32\UnitsOfMeasure\Runtime;

class CachedRuntimeSpec extends ObjectBehavior
{

    public function let(Runtime $runtime): void
    {
        $this->beConstructedWith($runtime);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CachedRuntime::class);
    }

    public function it_should_cache_result_of_parse(Runtime $runtime, MeasureUnit $m3, MeasureUnit $s): void
    {
        $runtime->parse('m3')->willReturn($m3)->shouldBeCalledOnce();
        $runtime->parse('s')->willReturn($s)->shouldBeCalledOnce();

        $this->parse('s')->shouldReturn($s);
        $this->parse('s')->shouldReturn($s);
        $this->parse('m3')->shouldReturn($m3);
        $this->parse('m3')->shouldReturn($m3);
    }

    public function it_should_clear_cache(Runtime $runtime, MeasureUnit $m3): void
    {
        $runtime->parse('m3')->willReturn($m3)->shouldBeCalledTimes(2);

        $this->parse('m3')->shouldReturn($m3);
        $this->parse('m3')->shouldReturn($m3);

        $this->clear();

        $this->parse('m3')->shouldReturn($m3);
        $this->parse('m3')->shouldReturn($m3);
    }

    public function it_should_proxy_invoke_for_unit(Runtime $runtime, MeasureUnit $m3, Quantity $quantity): void
    {
        $runtime->__invoke(1, $m3)->willReturn($quantity)->shouldBeCalledTimes(2);

        $this->__invoke(1, $m3)->shouldReturn($quantity);
        $this->__invoke(1, $m3)->shouldReturn($quantity);
    }

    public function it_should_proxy_invoke_and_parse_for_string(Runtime $runtime, MeasureUnit $m3, Quantity $quantity
    ): void {
        $runtime->__invoke(1, $m3)->willReturn($quantity)->shouldBeCalledTimes(2);

        $runtime->parse('m3')->willReturn($m3)->shouldBeCalledOnce();

        $this->__invoke(1, 'm3')->shouldReturn($quantity);
        $this->__invoke(1, 'm3')->shouldReturn($quantity);
    }

    public function it_should_cache_result_of_offset_get(Runtime $runtime, MeasureUnit $m3): void
    {
        $runtime->parse('m3')->willReturn($m3)->shouldBeCalledOnce();

        $this->offsetGet('m3')->shouldReturn($m3);
        $this->offsetGet('m3')->shouldReturn($m3);
    }

    public function it_should_throw_exception_for_offset_unset(): void
    {
        $this->shouldThrow(MethodNotSupportedException::class)->duringOffsetUnset('m3');
    }

    public function it_should_throw_exception_for_offset_exists(): void
    {
        $this->shouldThrow(MethodNotSupportedException::class)->duringOffsetExists('m3');
    }

    public function it_should_throw_exception_for_offset_set(): void
    {
        $this->shouldThrow(MethodNotSupportedException::class)->duringOffsetSet('m3', 'whatever');
    }
}
