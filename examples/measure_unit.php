<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

// To instantiate a measre unit, use array syntax or parse() method
$liter = $unitsOfMeasure['l'];
$deciliter = $unitsOfMeasure->parse('dl');

$volume = 23;

// To convert a value from one unit to another, first calculate a ratio of conversion,
// then apply that ratio to a value
echo "$volume $liter = {$liter->in($deciliter)->applyTo($volume)} $deciliter\n";

$meterCubed = $unitsOfMeasure['m3'];

echo "$volume $liter = {$liter->in($meterCubed)->applyTo($volume)} $meterCubed\n";

$meterCubedPer15Minutes = $unitsOfMeasure['m3.15 min-1'];
$literPerSecond = $unitsOfMeasure['l.s-1'];

$flow = 34;

// __toString representation is useful for debugging, but does not produce nice formatting of derived units
echo "$flow $meterCubedPer15Minutes = {$meterCubedPer15Minutes->in($literPerSecond)->applyTo($flow)} $literPerSecond\n";

// Formatter is a better choice to format measure unit
echo "$flow {$measureUnitFormatter->format($meterCubedPer15Minutes)} = {$meterCubedPer15Minutes->in($literPerSecond)->applyTo($flow)} {$measureUnitFormatter->format($literPerSecond)}\n";


$hertz = $unitsOfMeasure['Hz'];
$microSecond = $unitsOfMeasure['us'];

$frequency = 250000;

// Inversion of measure unit is respected
echo "$frequency $hertz = {$hertz->in($microSecond)->applyTo($frequency)} $microSecond\n";
echo "$frequency {$measureUnitFormatter->format($hertz)} = {$hertz->in($microSecond)->applyTo($frequency)} {$measureUnitFormatter->format($microSecond)}\n";

$kilogram = $unitsOfMeasure['kg'];
$meter = $unitsOfMeasure['m'];
$secondSquared = $unitsOfMeasure['s2'];

// Operations like multiply and divide are implemented for measure unit
$newton = $kilogram->multiplyBy($meter)->divideBy($secondSquared);

echo "$kilogram * $meter / $secondSquared = $newton = {$newton->in('N')->applyTo(1)} N\n";
