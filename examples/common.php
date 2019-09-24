<?php

declare(strict_types=1);

use Ds\Map;
use Zlikavac32\UnitsOfMeasure\CachedRuntime;
use Zlikavac32\UnitsOfMeasure\Decimal;
use Zlikavac32\UnitsOfMeasure\Defaults;
use Zlikavac32\UnitsOfMeasure\MapUnitNormalizer;
use Zlikavac32\UnitsOfMeasure\NativeRuntime;
use Zlikavac32\UnitsOfMeasure\NumberFormatQuantityFormatter;
use Zlikavac32\UnitsOfMeasure\SiFormParser;
use Zlikavac32\UnitsOfMeasure\SiUtf8StyleMeasureUnitFormatter;
use Zlikavac32\UnitsOfMeasure\Transition;
use Zlikavac32\UnitsOfMeasure\TransitionUnit;

require_once __DIR__ . '/../vendor/autoload.php';

// do note that this uses floats so currency are used
// here from the analysis standpoint
$baseUnits = Defaults::siBaseUnits()
                     ->merge(['kn']);

$transitionMap = Defaults::siDerivedUnits()
                         ->merge(Defaults::otherMetricUnits())
                         ->merge(
                             new Map(
                                 [
                                     'euro' => new Transition(
                                         new Decimal(7.13), new TransitionUnit('kn')
                                     ),
                                     'usd' => new Transition(
                                         new Decimal(6.9), new TransitionUnit('kn')
                                     ),
                                 ]
                             )
                         );

$imperialUnits = Defaults::imperialUnits();

$parser = new SiFormParser($baseUnits->merge($transitionMap->keys())->toArray(), $imperialUnits->keys()->toArray());

$measureUnitFormatter = new SiUtf8StyleMeasureUnitFormatter();
$quantityFormatter = new NumberFormatQuantityFormatter($measureUnitFormatter);

$normalizer = new MapUnitNormalizer(
    $transitionMap->merge($imperialUnits),
    $baseUnits
);

// Measure units are immutable so cached runtime can speed up parsing of measure units
$unitsOfMeasure = new CachedRuntime(
    new NativeRuntime(
        $parser,
        $normalizer
    )
);
