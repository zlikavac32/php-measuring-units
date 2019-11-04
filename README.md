# Units of measure for PHP

Library to manage various measure units, quantities, and conversions between them.

## Table of contents

1. [Introduction](#introduction)
1. [Installation](#installation)
1. [Configuration](#configuration)
1. [Usage](#usage)
    1. [Manipulating measure units](#manipulating-measure-units)
    1. [Quantities](#quantities)
    1. [Scale](#scale)
1. [Limitations](#limitations)
1. [Examples](#examples)

## Introduction

In the real world, a number on its own does not carry a lot of meaning. We usually associate a certain dimension with it like the length or the volume. Every dimension has its units of measures. By combining value with the dimension, we get a quantity.

Let's take a simple example of calculating a `sin` of an angle.

```php
$angle = 90;

echo sin($angle);
```

We get `0.89399666360056` which is not one, and this is an example of a mismatched measure unit. Implementation of `sin` expects the value to be provided in radians while we provide in degrees.  It's easy to make one such mistake so a better environment is needed.

Let's now imagine we have a concept of the quantity in PHP. 

```php
$angle = new Quantity(90, 'degc');

echo sin($angle->in('rad')->value());
```

Now it's much harder to make the previous mistake. If the dimension of the provided `$angle` is not the same as `rad`, this will fail. If the dimension is the same, then the value of `90` will be converted to a requested measure unit.

We could also have a `sin` function that directly accepts a quantity. 

All in all, if you, for some reason, handle quantities in various units of measure, it's important to maintain consistency, So the goal of this library is to make manipulation of quantities and measure units easier and bring that consistency into the code.

It's important to note that this library does not yet implement the concept of dimension. Only the concept of measure unit is implemented. For example, both radians and steradians have the same representation in the terms of SI units (no unit), but we can not add them or subtract them. This library does not make this distinction yet.

## Installation

The recommended installation is through Composer.

```
composer require zlikavac32/php-measure-units
```

## Configuration

To work with measure units, some services must be constructed.

```php
use Zlikavac32\UnitsOfMeasure\Defaults;
use Zlikavac32\UnitsOfMeasure\MapUnitNormalizer;
use Zlikavac32\UnitsOfMeasure\NativeRuntime;
use Zlikavac32\UnitsOfMeasure\SiFormParser;

$baseUnits = Defaults::siBaseUnits();

$transitionMap = Defaults::siDerivedUnits()
                         ->merge(Defaults::otherMetricUnits());

$imperialUnits = Defaults::imperialUnits();

$parser = new SiFormParser($baseUnits->merge($transitionMap->keys())->toArray(), $imperialUnits->keys()->toArray());

$normalizer = new MapUnitNormalizer(
    $transitionMap->merge($imperialUnits),
    $baseUnits
);

$unitsOfMeasure = new NativeRuntime(
  $parser,
  $normalizer
);
```

Variable `$unitsOfMeasure` is the main entry point for measurement units. `NativeRuntime` implements `\Zlikavac32\UnitsOfMeasure\Runtime` which can be used to create measure units and quantities.

Default implementation that is provided is `\Zlikavac32\UnitsOfMeasure\NativeRuntime` and it requires an instance of `\Zlikavac32\UnitsOfMeasure\Parser` and an instance of `\Zlikavac32\UnitsOfMeasure\Normalizer`.

The default implementation for the normalizer is `\Zlikavac32\UnitsOfMeasure\MapUnitNormalizer` which requires a map of transitions and a set of base units.

There are two implementations of the parser. One that uses canonical SI form like `kg.m` or `m.s-2` (`\Zlikavac32\UnitsOfMeasure\SiFormParser`) and the other uses more human form like `m/s` or `kg m/s2` (`\Zlikavac32\UnitsOfMeasure\HumanFormUnitsParser`).

`\Zlikavac32\UnitsOfMeasure\Defaults` contains a set of predefined base measure units as well as various derived units.

## Usage

Once the measure unit environment is created, one can create a measure unit instance by calling `parse(string $measureUnit)` method which returns an instance of `\Zlikavac32\UnitsOfMeasure\MeasureUnit`.

```php
echo (string) $unitsOfMeasure->parse('kg.m.s-2), "\n";
```

Instead of calling `parse()`, it's possible to use array syntax to retrieve a measuring unit.

```php
echo (string) $unitsOfMeasure['kg.m.s-2'], "\n";
```

### Manipulating measure units

Measure units can be multiplied, divided, raised to a power, inverted or converted between each other. Measure unit interface is straight forward, except `in(string|MeasureUnit $measureUnit)` method. It returns an instance of `\Zlikavac32\UnitsOfMeasure\Ratio`. To properly define a ratio, it's not enough just to have a number. We must also keep track of whether conversion inverts original units. For example, when converting `Hz` to `s`, the ratio is one, but the unit is globally inverted. So converting `2 Hz` is actually `0.5 s`.

```php
$m = $unitsOfMeasure['m'];
$kg = $unitsOfMeasure['kg'];
$s2 = $unitsOfMeasure['s2'];

$newton = $m->multiplyBy($kg)->divideBy($s2);

echo $newton->in('kN')->applyTo(5);
``` 

Check [Examples](#examples) for more info.

### Quantities

Instead of manually tracking value and its measure unit, they can be paired in a quantity.

Quantities can be created using `__invoke(int $value, string|MeasureUnit $measureUnit)` method. It returns an instance of `\Zlikavac32\UnitsOfMeasure\Quantity`.

```php
echo (string) $unitsOfMeasure(3, 'm3'), "\n";

echo (string) $unitsOfMeasure(3, $unitsOfMeasure['s']), "\n";
```

Quantities can also be multiplied, divided, raised to a power, etc. 

```php
$m = $unitsOfMeasure(1, 'm');
$kg = $unitsOfMeasure(1, 'kg');
$s2 = $unitsOfMeasure(1, 's2');

$newton = $m->multiplyBy($kg)->divideBy($s2);

echo (string) $newton->in('kN');
```

When performing operations like multiply or divide, the resulting measure unit is not automatically reduced. For example, multiplying `kg`, with `m`, and then dividing by `s2`, will not be `N`. Measure unit is representing `N`, but it is not `N`. To get a measuring unit that is `N`, one must make an explicit call to the `in()` method.

```php
echo (string) $newton, "\n";
echo (string) $newton->in('N');
```

Check [Examples](#examples) for more info.

### Scale

This library also supports scaled measure units. For example, the measuring unit for gasoline consumption can be `l/100 km`. One other example is fluid flow like `m3/15 min`.

```php
$gasolineConsumptionPer100Km = $unitsOfMeasure['l.100 km-1'];

// How many kilometers can we make with one liter if we had consumption of 4 liters per 100 kilometers
echo $gasolineConsumptionPer100Km->in('km.l-1')->applyTo(4);
```

To some extent, this can be used for direct measure unit conversion.

```php
echo $unitsOfMeasure['123 cm']->in('m')->ratio()->asFloat(), "\n";
echo $unitsOfMeasure['500 kg.6 m.2 s-2']->in('kN')->ratio()->asFloat();
```

Different runtime operations could support different measure unit formats, so one might implement runtime that parses `100cm+1mm` as `101 cm` directly. 

## Limitations

One limitation mentioned above is that the user of this library must provide semantic checks to measure unit operations. This library will not complain if you try to add radians with steradians since they have the same SI uni representation.

Another limitation is that this library handles only measure unit conversions with a constant ratio. That being said, conversion from degrees Celsius to Kelvin is not supported out of the box. Custom runtime can still be provided to accommodate that need.

## Examples

You can see more examples with code comments in [examples](/examples).
