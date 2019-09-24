<?php
declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure\Tests\Integration;

use Ds\Map;
use Ds\Set;
use PHPUnit\Framework\TestCase;
use Zlikavac32\UnitsOfMeasure\ConversionException;
use Zlikavac32\UnitsOfMeasure\Decimal;
use Zlikavac32\UnitsOfMeasure\MapUnitNormalizer;
use Zlikavac32\UnitsOfMeasure\MetricPrefix;
use Zlikavac32\UnitsOfMeasure\NativeRuntime;
use Zlikavac32\UnitsOfMeasure\Ratio;
use Zlikavac32\UnitsOfMeasure\Runtime;
use Zlikavac32\UnitsOfMeasure\SiFormParser;
use Zlikavac32\UnitsOfMeasure\Transition;
use Zlikavac32\UnitsOfMeasure\TransitionUnit;

class NativeRuntimeTest extends TestCase
{

    /**
     * @var Runtime
     */
    private $unitsOfMeasure;

    protected function setUp(): void
    {
        $baseUnits = new Set(['m', 'g', 's']);
        $transitionMap = new Map(
            [
                'J' => new Transition(
                    Decimal::ONE(), new TransitionUnit('N'), new TransitionUnit('m')
                ),
                'N' => new Transition(
                    Decimal::ONE(), new TransitionUnit('g', 1, MetricPrefix::KILO()), new TransitionUnit('m', 1),
                    new TransitionUnit('s', -2)
                ),
                'l' => new Transition(new Decimal(1, -3), new TransitionUnit('m', 3)),
                'Hz' => new Transition(new Decimal(1), new TransitionUnit('s', -1)),
            ]
        );

        $parser = new SiFormParser(
            $baseUnits->merge($transitionMap->keys())->toArray(), []
        );

        $normalizer = new MapUnitNormalizer(
            $transitionMap,
            $baseUnits
        );

        $this->unitsOfMeasure = new NativeRuntime(
            $parser,
            $normalizer
        );
    }

    protected function tearDown(): void
    {
        $this->unitsOfMeasure = null;
    }

    /**
     * @test
     */
    public function measure_units_can_be_parsed(): void
    {
        self::assertTrue($this->unitsOfMeasure['l']->equalsTo($this->unitsOfMeasure['10 dl']));
        self::assertTrue($this->unitsOfMeasure['m2']->equalsTo($this->unitsOfMeasure['m.m']));
        self::assertTrue($this->unitsOfMeasure['N']->equalsTo($this->unitsOfMeasure['kg.m.s-2']));
    }

    /**
     * @test
     */
    public function measure_units_can_be_multiplied(): void
    {
        self::assertTrue(
            $this->unitsOfMeasure['m']->multiplyBy($this->unitsOfMeasure['m'])->equalsTo($this->unitsOfMeasure['m2'])
        );
        self::assertTrue(
            $this->unitsOfMeasure['100 m']->multiplyBy($this->unitsOfMeasure['10 m'])->equalsTo(
                $this->unitsOfMeasure['1000 m2']
            )
        );
        self::assertTrue(
            $this->unitsOfMeasure['100 m']->multiplyBy($this->unitsOfMeasure['10 m-1'])->equalsTo(
                $this->unitsOfMeasure['10']
            )
        );
        self::assertTrue(
            $this->unitsOfMeasure['kg.s-2']->multiplyBy($this->unitsOfMeasure['m'])->equalsTo(
                $this->unitsOfMeasure['N']
            )
        );
    }

    /**
     * @test
     */
    public function measure_units_can_be_divided(): void
    {
        self::assertTrue(
            $this->unitsOfMeasure['m']->divideBy($this->unitsOfMeasure['m'])->equalsTo($this->unitsOfMeasure['1'])
        );
        self::assertTrue(
            $this->unitsOfMeasure['100 m']->divideBy($this->unitsOfMeasure['10 m'])->equalsTo(
                $this->unitsOfMeasure['10']
            )
        );
        self::assertTrue(
            $this->unitsOfMeasure['J']->divideBy($this->unitsOfMeasure['m'])->equalsTo($this->unitsOfMeasure['N'])
        );
    }

    /**
     * @test
     */
    public function measure_units_can_be_converted(): void
    {
        self::assertRatioIsSame(1000, false, $this->unitsOfMeasure['km']->in('m'));
        self::assertRatioIsSame(1000, true, $this->unitsOfMeasure['km']->in('m-1'));
        self::assertRatioIsSame(2, true, $this->unitsOfMeasure['2 Hz']->in('s'));
    }

    /**
     * @test
     */
    public function quantities_can_be_converted(): void
    {
        self::assertTrue(
            ($this->unitsOfMeasure)(2, 'Hz')->in('s')->equalsTo(0.5, 's')
        );
        self::assertTrue(
            ($this->unitsOfMeasure)(101, 'cm')->in('m')->equalsTo(1.01, 'm')
        );
    }

    /**
     * @test
     */
    public function quantities_should_be_added(): void
    {
        self::assertTrue(
            ($this->unitsOfMeasure)(1, 'm')->add(1, 'dm')->equalsTo(11, 'dm')
        );
        self::assertTrue(
            ($this->unitsOfMeasure)(1, 'N')->add(2, 'kg.m.s-2')->equalsTo(3, 'J.m-1')
        );
    }

    /**
     * @test
     */
    public function quantities_should_be_subtracted(): void
    {
        self::assertTrue(
            ($this->unitsOfMeasure)(1, 'm')->subtract(1, 'dm')->equalsTo(9, 'dm')
        );
        self::assertTrue(
            ($this->unitsOfMeasure)(1, 'N')->subtract(2, 'kg.m.s-2')->equalsTo(-1, 'J.m-1')
        );
    }

    /**
     * @test
     */
    public function quantities_should_be_multiplied(): void
    {
        self::assertTrue(
            ($this->unitsOfMeasure)(3, 'm')->multiplyBy(4, 'dm')->equalsTo(12, 'm.dm')
        );
        self::assertTrue(
            ($this->unitsOfMeasure)(3, 'm')->multiplyBy(4, 'dm')->equalsTo(12 / 10, 'm2')
        );
        self::assertTrue(
            ($this->unitsOfMeasure)(3, 'm')->multiplyBy(4, 'dm')->equalsTo(12 * 10, 'dm2')
        );
        self::assertTrue(
            ($this->unitsOfMeasure)(3, 'm')->multiplyBy(4, 'dm-1')->equalsTo(12 * 10, '1')
        );
        self::assertTrue(
            ($this->unitsOfMeasure)(2, 'N')->multiplyBy(3, 'm')->equalsTo(6, 'J')
        );
    }

    /**
     * @test
     */
    public function quantities_should_be_divided(): void
    {
        self::assertTrue(
            ($this->unitsOfMeasure)(8, 'm')->divideBy(4, 'm')->equalsTo(2, '1')
        );
        self::assertTrue(
            ($this->unitsOfMeasure)(8, 'm')->divideBy(4, 'dm-1')->equalsTo(2 * 10, 'dm2')
        );
        self::assertTrue(
            ($this->unitsOfMeasure)(8, 'm')->divideBy(4, 'dm-1')->equalsTo(2 / 10, 'm2')
        );
        self::assertTrue(
            ($this->unitsOfMeasure)(6, 'J')->divideBy(3, 'm')->equalsTo(2, 'N')
        );
    }

    /**
     * @test
     * @dataProvider incompatibleUnits
     */
    public function incompatible_units_throw_exception(string $from, string $to): void
    {
        $from = $this->unitsOfMeasure[$from];
        $to = $this->unitsOfMeasure[$to];

        $this->expectExceptionObject(new ConversionException($from, $to));
        $from->in($to);

        $this->fail('Should not reach here');
    }

    public function incompatibleUnits(): array
    {
        return [
            'missing unit' => ['kg', ''],
            'incompatible exponent' => ['m2', 'm'],
            'incompatible derived units exponent' => ['N', 'kg.m.s-3']
        ];
    }

    private function assertRatioIsSame(float $value, bool $inverted, Ratio $ratio, float $eps = 1e-15): void
    {
        self::assertSame($inverted, $ratio->inverted());
        self::assertTrue($ratio->ratio()->equalsTo($value, $eps));
    }
}
