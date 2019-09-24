<?php

declare(strict_types=1);

namespace spec\Zlikavac32\UnitsOfMeasure;

use PhpSpec\ObjectBehavior;
use Zlikavac32\UnitsOfMeasure\Decimal;
use Zlikavac32\UnitsOfMeasure\DivisionByZeroException;

class DecimalSpec extends ObjectBehavior
{

    public function it_is_initializable(): void
    {
        $this->beConstructedWith(1);

        $this->shouldHaveType(Decimal::class);
    }

    public function it_should_create_number_with_positive_exponent(): void
    {
        $this->beConstructedWith(0.001234, 4);

        $this->asFloat()->shouldBeApproximately(12.34, 1e-9);
    }

    public function it_should_create_number_with_negative_exponent(): void
    {
        $this->beConstructedWith(12.3, -5);

        $this->asFloat()->shouldBeApproximately(0.000123, 1e-9);
    }

    public function it_should_invert_number_greater_than_ten(): void
    {
        $this->beConstructedWith(16);

        $this->inverse()->asFloat()->shouldBeApproximately(0.0625, 1e-9);
    }

    public function it_should_invert_number_less_than_one(): void
    {
        $this->beConstructedWith(0.0625);

        $this->inverse()->asFloat()->shouldBeApproximately(16, 1e-9);
    }

    public function it_should_invert_negative_number(): void
    {
        $this->beConstructedWith(-2);

        $this->inverse()->asFloat()->shouldBeApproximately(-0.5, 1e-9);
    }

    public function it_should_invert_one_to_itself(): void
    {
        $this->beConstructedWith(1);

        $this->inverse()->shouldReturn($this);
    }

    public function it_should_equal_to_an_other_decimal(): void
    {
        $this->beConstructedWith(1.4);

        $this->equalsTo(new Decimal(14, -1))->shouldReturn(true);
    }

    public function it_should_throw_exception_on_inverse_of_zero(): void
    {
        $this->beConstructedWith(0);

        $this->shouldThrow(DivisionByZeroException::class)->duringInverse();
    }

    public function it_should_multiply_as_other_when_this_is_one(): void
    {
        $this->beConstructedWith(1);

        $other = new Decimal(2);

        $this->multiplyBy($other)->shouldReturn($other);
    }

    public function it_should_multiply_as_this_when_other_is_one(): void
    {
        $this->beConstructedWith(2);

        $this->multiplyBy(new Decimal(1))->shouldReturn($this);
    }

    public function it_should_multiply_decimal(): void
    {
        $this->beConstructedWith(16.25);

        $this->multiplyBy(new Decimal(0.00625))->asFloat()->shouldBeApproximately(0.1015625, 1e-9);
    }

    public function it_should_divide_as_other_inverse_when_this_is_one(): void
    {
        $this->beConstructedWith(1);

        $other = new Decimal(2);

        $this->divideBy($other)->asFloat()->shouldBeApproximately(0.5, 1e-9);
    }

    public function it_should_divide_as_this_when_other_is_one(): void
    {
        $this->beConstructedWith(2);

        $this->divideBy(new Decimal(1))->shouldReturn($this);
    }

    public function it_should_have_negative_sign(): void
    {
        $this->beConstructedWith(-235, 2);

        $this->sign()->shouldReturn(-1);
    }

    public function it_should_have_positive_sign(): void
    {
        $this->beConstructedWith(235, 2);

        $this->sign()->shouldReturn(1);
    }

    public function it_should_have_zero_sign(): void
    {
        $this->beConstructedWith(0);

        $this->sign()->shouldReturn(0);
    }

    public function it_should_divide_decimal(): void
    {
        $this->beConstructedWith(16.25);

        $this->divideBy(new Decimal(0.00625))->asFloat()->shouldBeApproximately(2600, 1e-9);
    }

    public function it_should_throw_exception_on_division_by_zero(): void
    {
        $this->beConstructedWith(1);

        $this->shouldThrow(DivisionByZeroException::class)->duringDivideBy(new Decimal(0));
    }

    public function it_should_pow_as_this_when_this_is_zero(): void
    {
        $this->beConstructedWith(1);

        $this->pow(23)->shouldReturn($this);
    }

    public function it_should_pow_to_one_when_exponent_is_zero(): void
    {
        $this->beConstructedWith(1);

        $this->pow(0)->asFloat()->shouldReturn(1.);
    }

    public function it_should_pow_as_this_when_exponent_is_one(): void
    {
        $this->beConstructedWith(1);

        $this->pow(23)->shouldReturn($this);
    }

    public function it_should_pow_zero_to_one(): void
    {
        $this->beConstructedWith(12);

        $this->pow(0)->shouldReturn(Decimal::ONE());
    }

    public function it_should_pow_to_exponent(): void
    {
        $this->beConstructedWith(4.4);

        $this->pow(3)->asFloat()->shouldBeApproximately(85.184, 1e-9);
    }

    public function it_should_check_whether_positive_decimal_is_in_interval(): void
    {
        $this->beConstructedWith(0.000123);

        $this->equalsTo(0.000123)->shouldReturn(true);
        $this->equalsTo(0.000124, 1e-4)->shouldReturn(false);
        $this->equalsTo(0.000124, 1e-3)->shouldReturn(true);
    }

    public function it_should_check_whether_negative_decimal_is_in_interval(): void
    {
        $this->beConstructedWith(-0.000123);

        $this->equalsTo(-0.000123)->shouldReturn(true);
        $this->equalsTo(-0.000124, 1e-4)->shouldReturn(false);
        $this->equalsTo(-0.000124, 1e-3)->shouldReturn(true);
    }

    public function it_should_compare_with_int(): void
    {
        $this->beConstructedWith(1);

        $this->compareTo(1)->shouldReturn(0);
        $this->compareTo(0)->shouldReturn(1);
        $this->compareTo(2)->shouldReturn(-1);
    }

    public function it_should_compare_with_float(): void
    {
        $this->beConstructedWith(1.234567);

        $this->compareTo(1.234567)->shouldReturn(0);
        $this->compareTo(1.234568, 1e-6)->shouldReturn(0);
        $this->compareTo(1.234568, 1e-7)->shouldReturn(-1);
        $this->compareTo(0)->shouldReturn(1);
        $this->compareTo(2)->shouldReturn(-1);
    }

    public function it_should_compare_with_decimal(): void
    {
        $this->beConstructedWith(1.234567);

        $this->compareTo(new Decimal(1.234567))->shouldReturn(0);
        $this->compareTo(new Decimal(1.234568), 1e-6)->shouldReturn(0);
        $this->compareTo(new Decimal(1.234568), 1e-7)->shouldReturn(-1);
        $this->compareTo(new Decimal(0))->shouldReturn(1);
        $this->compareTo(new Decimal(2))->shouldReturn(-1);
    }
}
