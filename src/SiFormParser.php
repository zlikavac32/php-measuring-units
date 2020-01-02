<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use Ds\Vector;
use LogicException;

final class SiFormParser implements Parser
{

    private const MEASURE_UNIT_REGEX_TEMPLATE = <<<'REGEX'
#
^
(?:
    %s
    (?<metric_prefix>%s)??
    (?<unit>%s)
)
(?<exponent>-?\d+)?
$
#x
REGEX;

    private string $regex;

    public function __construct(array $siUnits, array $nonSiUnits)
    {
        if (empty($siUnits)) {
            throw new LogicException('At least one SI unit must be defined');
        }

        $metricPrefixes = array_map(
            function (MetricPrefix $metricPrefix): string {
                return $metricPrefix->symbol();
            }, MetricPrefix::values()
        );

        usort($metricPrefixes, 'Zlikavac32\UnitsOfMeasure\cmp_string_prefix_desc');
        usort($siUnits, 'Zlikavac32\UnitsOfMeasure\cmp_string_prefix_desc');
        usort($nonSiUnits, 'Zlikavac32\UnitsOfMeasure\cmp_string_prefix_desc');

        $this->regex =
            sprintf(
                self::MEASURE_UNIT_REGEX_TEMPLATE,
                empty($nonSiUnits) ? '' : sprintf('(?<unit_alone>%s)|', implode('|', $nonSiUnits)),
                implode('|', $metricPrefixes),
                implode('|', $siUnits)
            );
    }

    /**
     * @inheritDoc
     */
    public function parse(string $measureUnit): array
    {
        return (new Vector(preg_split('/\.(?=(?:\d+ )?[a-zA-Z])/', $measureUnit)))
            ->map(
                function (string $unit) use ($measureUnit): MeasureUnitComponent {
                    $parts = explode(' ', $unit);

                    if (count($parts) > 2) {
                        throw new ParseException($measureUnit);
                    }

                    $factor = Decimal::ONE();
                    $unit = $parts[0];

                    if (isset($parts[1])) {
                        if (!preg_match('/^\d+(?:\.\d+)?$/', $parts[0])) {
                            throw new ParseException($measureUnit);
                        }

                        $factor = new Decimal((float) $parts[0]);
                        $unit = $parts[1];
                    }

                    if (!preg_match($this->regex, $unit, $matches)) {
                        throw new ParseException($measureUnit);
                    }

                    if (!empty($matches['unit_alone'])) {
                        return new StaticMeasureUnitComponent(
                            $factor, MetricPrefix::NONE(),
                            $matches['unit_alone'],
                            (int) ($matches['exponent'] ?? 1)
                        );
                    }

                    return new StaticMeasureUnitComponent(
                        $factor, MetricPrefix::valueOfSymbol($matches['metric_prefix']),
                        $matches['unit'],
                        (int) ($matches['exponent'] ?? 1)
                    );
                }
            )->toArray();
    }
}
