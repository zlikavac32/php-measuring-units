<?php

declare(strict_types=1);

use Zlikavac32\UnitsOfMeasure\Quantity;

require_once __DIR__ . '/common.php';

function print_convert_unit(string $from, string $to): void
{
    global $unitsOfMeasure, $measureUnitFormatter;

    $from = $unitsOfMeasure[$from];
    $to = $unitsOfMeasure[$to];

    printf(
        "1 %s = %s %s\n", $measureUnitFormatter->format($from), $from->in($to)->ratio()->asFloat(),
        $measureUnitFormatter->format($to)
    );
}

function print_multiply_quantity(string $destinationUnit, Quantity ...$quantities): void
{
    global $quantityFormatter;

    $parts = [];

    $final = null;

    foreach ($quantities as $quantity) {
        $parts[] = $quantityFormatter->format($quantity);

        if (null === $final) {
            $final = $quantity;

            continue;
        }

        $final = $final->multiplyBy($quantity);
    }

    $final = $final->in($destinationUnit);

    printf("%s = %s\n", implode(' * ', $parts), $quantityFormatter->format($final));
}

function print_sum_quantity(string $destinationUnit, Quantity ...$quantities): void
{
    global $quantityFormatter;

    $parts = [];

    $final = null;

    foreach ($quantities as $quantity) {
        $parts[] = $quantityFormatter->format($quantity);

        if (null === $final) {
            $final = $quantity;

            continue;
        }

        $final = $final->add($quantity);
    }

    $final = $final->in($destinationUnit);

    printf("%s = %s\n", implode(' + ', $parts), $quantityFormatter->format($final));
}

print_convert_unit('l', 'dl');
print_convert_unit('dl', 'l');

$volume = $unitsOfMeasure(30000, 'l');

$pricePerVolume = $unitsOfMeasure(2, 'kn.m-3');

print_convert_unit('euro', 'kn');
print_convert_unit('usd', 'kn');

$flow = $unitsOfMeasure(2, 'm3.15 min-1');

print_multiply_quantity('l.s-1', $flow);
print_multiply_quantity('kn.15 min-1', $flow, $pricePerVolume);

print_multiply_quantity('m12', $unitsOfMeasure(2, 'm4'), $unitsOfMeasure(4, 'm2'), $unitsOfMeasure(3, 'm6'));
print_multiply_quantity('1', $unitsOfMeasure(2, 'm4'), $unitsOfMeasure(4, 'm-4'));

print_multiply_quantity('kn', $volume, $pricePerVolume);
print_multiply_quantity('euro', $volume, $pricePerVolume);
print_multiply_quantity('usd', $volume, $pricePerVolume);

print_multiply_quantity('N', $unitsOfMeasure(.5, 'kg'), $unitsOfMeasure(2, 'm.s-2'));

print_multiply_quantity('J', $unitsOfMeasure(1, 'N'), $unitsOfMeasure(1, 'm'));

print_multiply_quantity('W', $unitsOfMeasure(1, 'J'), $unitsOfMeasure(1, 's')->invert());

print_multiply_quantity('kW.h', $unitsOfMeasure(1, 'W'), $unitsOfMeasure(1000, 'h'));

print_multiply_quantity('kn', $unitsOfMeasure(1, 'kW.h'), $unitsOfMeasure(2, 'kW-1.h-1.kn'));
print_multiply_quantity('usd', $unitsOfMeasure(1, 'kW.h'), $unitsOfMeasure(2, 'kW-1.h-1.kn'));

print_multiply_quantity('deg', $unitsOfMeasure(pi(), 'rad'));

print_sum_quantity('m', $unitsOfMeasure(2, 'cm'), $unitsOfMeasure(1, 'm'));

print_convert_unit('12342 km.30 h-1', 'km.h-1');

print_multiply_quantity('dl-1', $unitsOfMeasure(5, 'l'));
print_multiply_quantity('ns', $unitsOfMeasure(3.7, 'GHz'));

print_convert_unit('perch', 'm2');
print_convert_unit('rood', 'm2');
print_convert_unit('acre', 'm2');

print_convert_unit('perch', 'ha');
print_convert_unit('rood', 'ha');
print_convert_unit('acre', 'ha');

print_convert_unit('th', 'm');

print_convert_unit('lea', 'mi');
print_convert_unit('mi', 'fur');
print_convert_unit('mi', 'km');
print_convert_unit('fur', 'ch');
print_convert_unit('ch', 'yd');
print_convert_unit('yd', 'ft');
print_convert_unit('ft', 'in');
print_convert_unit('ft', 'm');
print_convert_unit('in', 'th');

print_convert_unit('ftm', 'yd');
print_convert_unit('cable', 'ftm');
print_convert_unit('M', 'cable');
print_convert_unit('NM', 'cable');
print_convert_unit('nmi', 'cable');

print_convert_unit('link', 'in');
print_convert_unit('rod', 'link');

print_convert_unit('1 m.10 mm', 'ft2');

print_multiply_quantity('km.l-1', $unitsOfMeasure(4.4, 'l.100 km-1'));

print_sum_quantity('h', $unitsOfMeasure(1, 'h'), $unitsOfMeasure(15, 'min'));