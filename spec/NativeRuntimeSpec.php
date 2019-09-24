<?php

declare(strict_types=1);

namespace spec\Zlikavac32\UnitsOfMeasure;

use LogicException;
use PhpSpec\ObjectBehavior;
use Zlikavac32\UnitsOfMeasure\Decimal;
use Zlikavac32\UnitsOfMeasure\MeasureUnit;
use Zlikavac32\UnitsOfMeasure\MeasureUnitComponent;
use Zlikavac32\UnitsOfMeasure\MethodNotSupportedException;
use Zlikavac32\UnitsOfMeasure\NativeRuntime;
use Zlikavac32\UnitsOfMeasure\NormalizedMeasureUnitComponent;
use Zlikavac32\UnitsOfMeasure\Normalizer;
use Zlikavac32\UnitsOfMeasure\Parser;
use Zlikavac32\UnitsOfMeasure\StaticMeasureUnit;
use Zlikavac32\UnitsOfMeasure\StaticQuantity;

class NativeRuntimeSpec extends ObjectBehavior
{

    public function let(Parser $parser, Normalizer $normalizer): void
    {
        $this->beConstructedWith($parser, $normalizer);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(NativeRuntime::class);
    }

    public function it_should_parse_number(): void
    {
        $this->parse('12')->shouldBeLike(
            new StaticMeasureUnit(
                new Decimal(12), [], new Decimal(12), [], $this->getWrappedObject()
            )
        );
    }

    public function it_should_parse_unit(
        Parser $parser, Normalizer $normalizer, MeasureUnitComponent $unitComponent,
        NormalizedMeasureUnitComponent $normalizedUnitComponent
    ): void {
        $parser->parse('unit')->willReturn([$unitComponent]);

        $normalizer->normalize([$unitComponent])->willReturn([Decimal::ONE(), [$normalizedUnitComponent]]);

        $normalizedUnitComponent->abbrev()->willReturn('unit');

        $this->parse('unit')->shouldBeLike(
            new StaticMeasureUnit(
                Decimal::ONE(), [$unitComponent->getWrappedObject()], Decimal::ONE(),
                [$normalizedUnitComponent->getWrappedObject()], $this->getWrappedObject()
            )
        );
    }

    public function it_should_throw_exception_if_not_string_passed_to_offset_get(): void
    {
        $this->shouldThrow(LogicException::class)->duringOffsetGet(1);
    }

    public function it_should_parse_on_offset_get(
        Parser $parser, Normalizer $normalizer, MeasureUnitComponent $unitComponent,
        NormalizedMeasureUnitComponent $normalizedUnitComponent
    ): void {
        $parser->parse('unit')->willReturn([$unitComponent]);

        $normalizer->normalize([$unitComponent])->willReturn([Decimal::ONE(), [$normalizedUnitComponent]]);

        $normalizedUnitComponent->abbrev()->willReturn('unit');

        $this->parse('unit')->shouldBeLike(
            new StaticMeasureUnit(
                Decimal::ONE(), [$unitComponent->getWrappedObject()], Decimal::ONE(),
                [$normalizedUnitComponent->getWrappedObject()], $this->getWrappedObject()
            )
        );
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

    public function it_should_make_quantity_when_unit_provided(MeasureUnit $unit): void
    {
        $this->__invoke(2., $unit)->shouldBeLike(
            new StaticQuantity(2., $unit->getWrappedObject(), $this->getWrappedObject())
        );
    }

    public function it_should_parse_unit_when_string_passed_to_invoke(
        Parser $parser, Normalizer $normalizer, MeasureUnitComponent $unitComponent,
        NormalizedMeasureUnitComponent $normalizedUnitComponent
    ): void {
        $parser->parse('unit')->willReturn([$unitComponent]);

        $normalizer->normalize([$unitComponent])->willReturn([Decimal::ONE(), [$normalizedUnitComponent]]);

        $normalizedUnitComponent->abbrev()->willReturn('unit');

        $this->__invoke(2., 'unit')->shouldBeLike(
            new StaticQuantity(
                2., new StaticMeasureUnit(
                Decimal::ONE(), [$unitComponent->getWrappedObject()], Decimal::ONE(),
                [$normalizedUnitComponent->getWrappedObject()], $this->getWrappedObject()
            ), $this->getWrappedObject()
            )
        );
    }

    public function it_should_throw_exception_when_measure_unit_of_invalid_type(): void
    {
        $this->shouldThrow(LogicException::class)->during__invoke(2., 3);
    }
}
