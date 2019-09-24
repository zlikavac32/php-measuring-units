<?php
declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure\Tests\Integration;

use Ds\Map;
use Ds\Set;
use PHPUnit\Framework\TestCase;
use Zlikavac32\UnitsOfMeasure\CachedRuntime;
use Zlikavac32\UnitsOfMeasure\ChainedParser;
use Zlikavac32\UnitsOfMeasure\ConversionException;
use Zlikavac32\UnitsOfMeasure\Decimal;
use Zlikavac32\UnitsOfMeasure\Defaults;
use Zlikavac32\UnitsOfMeasure\MapUnitNormalizer;
use Zlikavac32\UnitsOfMeasure\MetricPrefix;
use Zlikavac32\UnitsOfMeasure\NativeRuntime;
use Zlikavac32\UnitsOfMeasure\NumberFormatQuantityFormatter;
use Zlikavac32\UnitsOfMeasure\Ratio;
use Zlikavac32\UnitsOfMeasure\Runtime;
use Zlikavac32\UnitsOfMeasure\SiFormParser;
use Zlikavac32\UnitsOfMeasure\SiUtf8StyleMeasureUnitFormatter;
use Zlikavac32\UnitsOfMeasure\Transition;
use Zlikavac32\UnitsOfMeasure\TransitionUnit;

class ConversionTest extends TestCase
{

    /**
     * @var Runtime
     */
    private $unitsOfMeasure;

    protected function setUp(): void
    {
        $baseUnits = Defaults::siBaseUnits();

        $transitionMap = Defaults::siDerivedUnits()
                                 ->merge(Defaults::otherMetricUnits());

        $imperialUnits = Defaults::imperialUnits();

        $parser = new ChainedParser(
            new SiFormParser($baseUnits->merge($transitionMap->keys())->toArray(), $imperialUnits->keys()->toArray())
        );

        $normalizer = new MapUnitNormalizer(
            $transitionMap->merge($imperialUnits),
            $baseUnits
        );

        $this->unitsOfMeasure = new NativeRuntime(
            $parser,
            $normalizer
        );
    }

    /**
     * @test
     * @dataProvider measureUnits
     */
    public function conversion_should_be_correct(string $from, string $to, float $ratioValue, bool $inverted = false): void
    {
        $this->assertRatioIsSame($ratioValue, $inverted, $this->unitsOfMeasure[$from]->in($to));
    }

    public function measureUnits(): array
    {
        return [
            'm to m = 1' => ['m', 'm', 1],
            'cm to m = 0.01' => ['cm', 'm', 0.01],
            'm to cm = 100' => ['m', 'cm', 100],
            'km/h to m/s = 3.6' => ['km.h-1', 'm.s-1', 10 / 36],
            '2 Hz to s = 2 and inverted' => ['2 Hz', 's', 2, true],
            'h to s = 3600' => ['h', 's', 3600],
            'h to min = 60' => ['h', 'min', 60],
            'au to m = 149597870700' => ['au', 'm', 149597870700],
            'deg to rad = 0.0174532925199433' => ['deg', 'rad', 0.0174532925199433],
            '90 deg to rad = 1.5707963267948966' => ['90 deg', 'rad', 1.5707963267948966],
            'ha to km2 = 0.1' => ['ha', 'km2', 0.1],
            'eV to J = 1.602176634 * 10^-19' => ['eV', 'J', 1.602176634e-19],
            'J/s to W = 1' => ['J.s-1', 'W', 1],
            'N/m2 to Pa = 1' => ['N.m-2', 'Pa', 1],
            'A s to C = 1' => ['A.s', 'C', 1],
            'W/A to V = 1' => ['W.A-1', 'V', 1],
            'C/V to F = 1' => ['C.V-1', 'F', 1],
            'V s to Wb = 1' => ['V.s', 'Wb', 1],
            'Wb/m2 to T = 1' => ['Wb.m-2', 'T', 1],
            'Wb/A to H = 1' => ['Wb.A-1', 'H', 1],
            'V/A to ohm = 1' => ['V.A-1', 'ohm', 1],
            '1/ohm to S = 1' => ['ohm-1', 'S', 1],
            'N m to J = 1' => ['N.m', 'J', 1],
            'kg m/s2 to N = 1' => ['kg.m.s-2', 'N', 1],
            'rad2 to sr = 1' => ['rad2', 'sr', 1],
            'cd/sr to lm = 1' => ['cd.sr-1', 'lm', 1],
            'lm/m2 to lx = 1' => ['lm.m-2', 'lx', 1],
            's-1 to Bq = 1' => ['s-1', 'Bq', 1],
            's to Bq = 1 and invert' => ['s', 'Bq', 1, true],
            'J/kg to Gy = 1' => ['J.kg-1', 'Gy', 1],
            'J/kg to Sv = 1' => ['J.kg-1', 'Sv', 1],
            'mol/s to kat = 1' => ['mol.s-1', 'kat', 1],
            't to kg = 1000' => ['t', 'kg', 1000],
            'L to dm3 = 1' => ['L', 'dm3', 1],
            'L to m3 = 0.001' => ['L', 'm3', 0.001],
            'l to L = 1' => ['l', 'L', 1],

            'bar to kPa = 100' => ['bar', 'kPa', 100],
            'at to Pa = 98066.5' => ['at', 'Pa', 98066.5],
            'atm to Pa = 101325' => ['atm', 'Pa', 101325],
            'mmHg to Pa = 133.322387415' => ['mmHg', 'Pa', 133.322387415],

            'th to m = 2.54e-5' => ['th', 'm', 2.54e-5],
            'in to m = 2.54e-2' => ['in', 'm', 0.0254],
            'ft to m = 0.3048' => ['ft', 'm', 0.3048],
            'yd to m = 0.9144' => ['yd', 'm', 0.9144],
            'ch to m = 20.12' => ['ch', 'm', 20.1168],
            'fur to m = 201.2' => ['fur', 'm', 201.168],
            'mi to m = 1609' => ['mi', 'm', 1609.344],
            'lea to m = 4828.032' => ['lea', 'm', 4828.032],
            'ftm to m = 1.8288' => ['ftm', 'm', 1.8288],
            'cable to m = 182.88' => ['cable', 'm', 182.88],
            'M to m = 1828.8' => ['M', 'm', 1828.8],
            'NM to M = 1' => ['NM', 'M', 1],
            'nmi to M = 1' => ['nmi', 'M', 1],
            'link to m = 0.201168' => ['link', 'm', 0.201168],
            'rod to m = 5.0292' => ['rod', 'm', 5.0292],
            'perch to m2 = 25.29285264' => ['perch', 'm2', 25.29285264],
            'rood to m2 = 1011.7141056' => ['rood', 'm2', 1011.7141056],
            'acre to m2 = 4046.8564224' => ['acre', 'm2', 4046.8564224],
            'rod2 to perch = 1' => ['rod2', 'perch', 1],
            'fur rod to rood = 1' => ['fur.rod', 'rood', 1],
            '2 fur 3 rod to 6 rood = 1' => ['2 fur.3 rod', '6 rood', 1],
            'ch fur to acre = 1' => ['ch.fur', 'acre', 1],
        ];
    }

    private function assertRatioIsSame(float $value, bool $inverted, Ratio $ratio, float $eps = 1e-15): void
    {
        self::assertSame($inverted, $ratio->inverted());
        self::assertTrue($ratio->ratio()->equalsTo($value, $eps), "Expected $value but got {$ratio->ratio()->asFloat()}");
    }
}
