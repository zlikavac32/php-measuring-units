<?php

declare(strict_types=1);

namespace spec\Zlikavac32\UnitsOfMeasure;

use LogicException;
use PhpSpec\ObjectBehavior;
use Zlikavac32\UnitsOfMeasure\MeasureUnit;
use Zlikavac32\UnitsOfMeasure\Quantity;
use Zlikavac32\UnitsOfMeasure\Ratio;
use Zlikavac32\UnitsOfMeasure\Runtime;
use Zlikavac32\UnitsOfMeasure\StaticQuantity;

class StaticQuantitySpec extends ObjectBehavior
{

    public function let(MeasureUnit $unit, Runtime $runtime): void
    {
        $this->beConstructedWith(1, $unit, $runtime);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(StaticQuantity::class);
    }

    public function it_should_apply_callback_to_value(
        MeasureUnit $unit, Runtime $runtime
    ): void {
        $this->beConstructedWith(4, $unit, $runtime);

        $this->apply('sqrt')->shouldBeLike(
            new StaticQuantity(
                2, $unit->getWrappedObject(), $runtime->getWrappedObject()
            )
        );
    }

    public function it_should_add_quantity(
        MeasureUnit $unit, Runtime $runtime, Quantity $quantity, MeasureUnit $otherUnit, Ratio $ratio
    ): void {
        $quantity->value()->willReturn(2);
        $quantity->measureUnit()->willReturn($otherUnit);

        $otherUnit->in($unit)->willReturn($ratio);

        $ratio->applyTo(2)->willReturn(20);

        $this->add($quantity)
             ->shouldBeLike(new StaticQuantity(21, $unit->getWrappedObject(), $runtime->getWrappedObject()));
    }

    public function it_should_add_value_and_string_unit(
        MeasureUnit $unit, Runtime $runtime, MeasureUnit $otherUnit, Ratio $ratio
    ): void {
        $runtime->parse('unit')->willReturn($otherUnit);

        $otherUnit->in($unit)->willReturn($ratio);

        $ratio->applyTo(2)->willReturn(20);

        $this->add(2, 'unit')
             ->shouldBeLike(new StaticQuantity(21, $unit->getWrappedObject(), $runtime->getWrappedObject()));
    }

    public function it_should_add_value_and_unit_instance(
        MeasureUnit $unit, Runtime $runtime, MeasureUnit $otherUnit, Ratio $ratio
    ): void {
        $otherUnit->in($unit)->willReturn($ratio);

        $ratio->applyTo(2)->willReturn(20);

        $this->add(2, $otherUnit)
             ->shouldBeLike(new StaticQuantity(21, $unit->getWrappedObject(), $runtime->getWrappedObject()));
    }

    public function it_should_subtract_quantity(
        MeasureUnit $unit, Runtime $runtime, Quantity $quantity, MeasureUnit $otherUnit, Ratio $ratio
    ): void {
        $quantity->value()->willReturn(2);
        $quantity->measureUnit()->willReturn($otherUnit);

        $otherUnit->in($unit)->willReturn($ratio);

        $ratio->applyTo(2)->willReturn(20);

        $this->subtract($quantity)
             ->shouldBeLike(new StaticQuantity(-19, $unit->getWrappedObject(), $runtime->getWrappedObject()));
    }

    public function it_should_subtract_value_and_string_unit(
        MeasureUnit $unit, Runtime $runtime, MeasureUnit $otherUnit, Ratio $ratio
    ): void {
        $runtime->parse('unit')->willReturn($otherUnit);

        $otherUnit->in($unit)->willReturn($ratio);

        $ratio->applyTo(2)->willReturn(20);

        $this->subtract(2, 'unit')
             ->shouldBeLike(new StaticQuantity(-19, $unit->getWrappedObject(), $runtime->getWrappedObject()));
    }

    public function it_should_subtract_value_and_unit_instance(
        MeasureUnit $unit, Runtime $runtime, MeasureUnit $otherUnit, Ratio $ratio
    ): void {
        $otherUnit->in($unit)->willReturn($ratio);

        $ratio->applyTo(2)->willReturn(20);

        $this->subtract(2, $otherUnit)
             ->shouldBeLike(new StaticQuantity(-19, $unit->getWrappedObject(), $runtime->getWrappedObject()));
    }

    public function it_should_multiply_quantity(
        MeasureUnit $unit, Runtime $runtime, Quantity $quantity, MeasureUnit $otherUnit, MeasureUnit $destUnit
    ): void {
        $this->beConstructedWith(3, $unit, $runtime);

        $quantity->value()->willReturn(2);
        $quantity->measureUnit()->willReturn($otherUnit);

        $unit->multiplyBy($otherUnit)->willReturn($destUnit);

        $this->multiplyBy($quantity)
             ->shouldBeLike(new StaticQuantity(6, $destUnit->getWrappedObject(), $runtime->getWrappedObject()));
    }

    public function it_should_multiply_value_and_string_unit(
        MeasureUnit $unit, Runtime $runtime, MeasureUnit $otherUnit, MeasureUnit $destUnit
    ): void {
        $this->beConstructedWith(3, $unit, $runtime);

        $runtime->parse('unit')->willReturn($otherUnit);

        $unit->multiplyBy($otherUnit)->willReturn($destUnit);

        $this->multiplyBy(2, 'unit')
             ->shouldBeLike(new StaticQuantity(6, $destUnit->getWrappedObject(), $runtime->getWrappedObject()));
    }

    public function it_should_multiply_value_and_unit_instance(
        MeasureUnit $unit, Runtime $runtime, MeasureUnit $otherUnit, MeasureUnit $destUnit
    ): void {
        $this->beConstructedWith(3, $unit, $runtime);

        $unit->multiplyBy($otherUnit)->willReturn($destUnit);

        $this->multiplyBy(2, $otherUnit)
             ->shouldBeLike(new StaticQuantity(6, $destUnit->getWrappedObject(), $runtime->getWrappedObject()));
    }

    public function it_should_divide_quantity(
        MeasureUnit $unit, Runtime $runtime, Quantity $quantity, MeasureUnit $otherUnit, MeasureUnit $destUnit
    ): void {
        $this->beConstructedWith(12, $unit, $runtime);

        $quantity->value()->willReturn(2);
        $quantity->measureUnit()->willReturn($otherUnit);

        $unit->divideBy($otherUnit)->willReturn($destUnit);

        $this->divideBy($quantity)
             ->shouldBeLike(new StaticQuantity(6, $destUnit->getWrappedObject(), $runtime->getWrappedObject()));
    }

    public function it_should_divide_value_and_string_unit(
        MeasureUnit $unit, Runtime $runtime, MeasureUnit $otherUnit, MeasureUnit $destUnit
    ): void {
        $this->beConstructedWith(12, $unit, $runtime);

        $runtime->parse('unit')->willReturn($otherUnit);

        $unit->divideBy($otherUnit)->willReturn($destUnit);

        $this->divideBy(2, 'unit')
             ->shouldBeLike(new StaticQuantity(6, $destUnit->getWrappedObject(), $runtime->getWrappedObject()));
    }

    public function it_should_divide_value_and_unit_instance(
        MeasureUnit $unit, Runtime $runtime, MeasureUnit $otherUnit, MeasureUnit $destUnit
    ): void {
        $this->beConstructedWith(12, $unit, $runtime);

        $unit->divideBy($otherUnit)->willReturn($destUnit);

        $this->divideBy(2, $otherUnit)
             ->shouldBeLike(new StaticQuantity(6, $destUnit->getWrappedObject(), $runtime->getWrappedObject()));
    }

    public function it_should_invert_quantity(
        MeasureUnit $unit, Runtime $runtime, MeasureUnit $destUnit
    ): void {
        $this->beConstructedWith(2, $unit, $runtime);

        $unit->invert()->willReturn($destUnit);

        $this->invert()
             ->shouldBeLike(new StaticQuantity(0.5, $destUnit->getWrappedObject(), $runtime->getWrappedObject()));
    }

    public function it_should_change_quantity_unit_for_string(
        MeasureUnit $unit, Runtime $runtime, MeasureUnit $destUnit, Ratio $ratio
    ): void {
        $this->beConstructedWith(2, $unit, $runtime);

        $runtime->parse('unit')->willReturn($destUnit);

        $unit->in($destUnit)->willReturn($ratio);

        $ratio->applyTo(2)->willReturn(4);

        $this->in('unit')
             ->shouldBeLike(new StaticQuantity(4, $destUnit->getWrappedObject(), $runtime->getWrappedObject()));
    }

    public function it_should_change_quantity_unit_instance(
        MeasureUnit $unit, Runtime $runtime, MeasureUnit $destUnit, Ratio $ratio
    ): void {
        $this->beConstructedWith(2, $unit, $runtime);

        $unit->in($destUnit)->willReturn($ratio);

        $ratio->applyTo(2)->willReturn(4);

        $this->in($destUnit)
             ->shouldBeLike(new StaticQuantity(4, $destUnit->getWrappedObject(), $runtime->getWrappedObject()));
    }
}
