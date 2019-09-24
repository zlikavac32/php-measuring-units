<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use Ds\Map;
use Ds\Set;

final class Defaults
{

    public static function siBaseUnits(): Set
    {
        return new Set(['m', 's', 'g', 'K', 'A', 'mol', 'cd']);
    }

    public static function siDerivedUnits(): Map
    {
        return new Map(
            [
                'au' => new Transition(
                    new Decimal(149597870700), new TransitionUnit('m')
                ),
                'deg' => new Transition(
                    new Decimal(pi() / 180), new TransitionUnit('rad')
                ),
                'ha' => new Transition(
                    new Decimal(10, 4), new TransitionUnit('m', 2)
                ),
                'eV' => new Transition(
                    new Decimal(1.602176634, -19), new TransitionUnit('J')
                ),
                'W' => new Transition(
                    Decimal::ONE(), new TransitionUnit('J'), new TransitionUnit('s', -1)
                ),
                'Pa' => new Transition(
                    Decimal::ONE(), new TransitionUnit('N'), new TransitionUnit('m', -2)
                ),
                'C' => new Transition(
                    Decimal::ONE(), new TransitionUnit('s'), new TransitionUnit('A')
                ),
                'V' => new Transition(
                    Decimal::ONE(), new TransitionUnit('W'), new TransitionUnit('A', -1)
                ),
                'F' => new Transition(
                    Decimal::ONE(), new TransitionUnit('C'), new TransitionUnit('V', -1)
                ),
                'Wb' => new Transition(
                    Decimal::ONE(), new TransitionUnit('V'), new TransitionUnit('s')
                ),
                'T' => new Transition(
                    Decimal::ONE(), new TransitionUnit('Wb'), new TransitionUnit('m', -2)
                ),
                'H' => new Transition(
                    Decimal::ONE(), new TransitionUnit('Wb'), new TransitionUnit('A', -1)
                ),
                'ohm' => new Transition(
                    Decimal::ONE(), new TransitionUnit('V'), new TransitionUnit('A', -1)
                ),
                'S' => new Transition(
                    Decimal::ONE(), new TransitionUnit('ohm', -1)
                ),
                'J' => new Transition(
                    Decimal::ONE(), new TransitionUnit('N'), new TransitionUnit('m')
                ),
                'N' => new Transition(
                    Decimal::ONE(), new TransitionUnit('g', 1, MetricPrefix::KILO()), new TransitionUnit('m'),
                    new TransitionUnit('s', -2)
                ),
                'sr' => new Transition(
                    Decimal::ONE(), new TransitionUnit('rad', 2)
                ),
                'rad' => new Transition(
                    Decimal::ONE()
                ),
                'lm' => new Transition(
                    Decimal::ONE(), new TransitionUnit('cd'), new TransitionUnit('sr')
                ),
                'lx' => new Transition(
                    Decimal::ONE(), new TransitionUnit('lm'), new TransitionUnit('m', -2)
                ),
                'Bq' => new Transition(
                    Decimal::ONE(), new TransitionUnit('s', -1)
                ),
                'Gy' => new Transition(
                    Decimal::ONE(), new TransitionUnit('J'), new TransitionUnit('g', -1, MetricPrefix::KILO())
                ),
                'Sv' => new Transition(
                    Decimal::ONE(), new TransitionUnit('J'), new TransitionUnit('g', -1, MetricPrefix::KILO())
                ),
                'kat' => new Transition(
                    Decimal::ONE(), new TransitionUnit('mol'), new TransitionUnit('s', -1)
                ),
                't' => new Transition(new Decimal(1, 6), new TransitionUnit('g')),
                'h' => new Transition(new Decimal(60), new TransitionUnit('min')),
                'min' => new Transition(new Decimal(60), new TransitionUnit('s')),
                'Hz' => new Transition(Decimal::ONE(), new TransitionUnit('s', -1)),
                'L' => new Transition(Decimal::ONE(), new TransitionUnit('l')),
                'l' => new Transition(new Decimal(1, -3), new TransitionUnit('m', 3)),
            ]
        );
    }

    public static function otherMetricUnits(): Map
    {
        return new Map(
            [
                'bar' => new Transition(new Decimal(1, 5), new TransitionUnit('Pa')),
                'at' => new Transition(new Decimal(98066.5), new TransitionUnit('Pa')),
                'atm' => new Transition(new Decimal(101325), new TransitionUnit('Pa')),
                'mmHg' => new Transition(new Decimal(133.322387415), new TransitionUnit('Pa')),
            ]
        );
    }

    public static function imperialUnits(): Map
    {
        return new Map(
            [
                'th' => new Transition(new Decimal(25.4), new TransitionUnit('m', 1, MetricPrefix::MICRO())),
                'in' => new Transition(new Decimal(25.4), new TransitionUnit('m', 1, MetricPrefix::MILLI())),
                'ft' => new Transition(new Decimal(304.8), new TransitionUnit('m', 1, MetricPrefix::MILLI())),
                'yd' => new Transition(new Decimal(914.4), new TransitionUnit('m', 1, MetricPrefix::MILLI())),
                'ch' => new Transition(new Decimal(20.1168), new TransitionUnit('m')),
                'fur' => new Transition(new Decimal(201.168), new TransitionUnit('m')),
                'mi' => new Transition(new Decimal(1609.344), new TransitionUnit('m')),
                'lea' => new Transition(new Decimal(4828.032), new TransitionUnit('m')),

                'ftm' => new Transition(new Decimal(1.8288), new TransitionUnit('m')),
                'cable' => new Transition(new Decimal(182.88), new TransitionUnit('m')),
                'M' => new Transition(new Decimal(1828.8), new TransitionUnit('m')),
                'NM' => new Transition(Decimal::ONE(), new TransitionUnit('M')),
                'nmi' => new Transition(Decimal::ONE(), new TransitionUnit('M')),

                'link' => new Transition(new Decimal(0.201168), new TransitionUnit('m')),
                'rod' => new Transition(new Decimal(5.0292), new TransitionUnit('m')),

                'perch' => new Transition(new Decimal(25.29285264), new TransitionUnit('m', 2)),
                'rood' => new Transition(new Decimal(1011.7141056), new TransitionUnit('m', 2)),
                'acre' => new Transition(new Decimal(4046.8564224), new TransitionUnit('m', 2)),
            ]
        );
    }
}
