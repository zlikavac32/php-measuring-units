<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

$liter = $unitsOfMeasure['l'];
$deciliter = $unitsOfMeasure['dl'];

// Quantities can be created by calling __invoke() method
$volume = $unitsOfMeasure(23, 'l');

// Quantities can be converted into different measure units
echo "$volume = {$volume->in('dl')}\n";
echo "$volume = {$volume->in('m3')}\n";

$meterCubedPer15Minutes = $unitsOfMeasure['m3.15 min-1'];
$literPerSecond = $unitsOfMeasure['l.s-1'];

$flow = $unitsOfMeasure(34, 'm3.15 min-1');

// Formatter for quantities is also provided
echo "$flow = {$flow->in('l.s-1')}\n";
echo "{$quantityFormatter->format($flow)} = {$quantityFormatter->format($flow->in('l.s-1'))}\n";


$hertz = $unitsOfMeasure['Hz'];
$microSecond = $unitsOfMeasure['us'];

$frequency = $unitsOfMeasure(250000, 'Hz');

echo "$frequency = {$frequency->in('us')}\n";
echo "{$quantityFormatter->format($frequency)} = {$quantityFormatter->format($frequency->in('us'))}\n";

$kilogram = $unitsOfMeasure(6, 'kg');
$meter = $unitsOfMeasure(2, 'km');
$secondSquared = $unitsOfMeasure(3, 's2');

$newton = $kilogram->multiplyBy($meter)->divideBy($secondSquared);

echo "{$quantityFormatter->format($kilogram)} * {$quantityFormatter->format($meter)} / {$quantityFormatter->format($secondSquared)} = {$quantityFormatter->format($newton)} = {$quantityFormatter->format($newton->in('N'))}\n";
