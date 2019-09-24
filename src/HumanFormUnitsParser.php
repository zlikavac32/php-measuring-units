<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

final class HumanFormUnitsParser implements Parser
{

    private const TOKENIZER_REGEX = <<<'REGEX'
#
\G
(?:
    (?<T_IDEN>(?:\d+(?:\.\d+)?\s+)?[a-zA-Z]+(?:-?\d+)?)
    |
    (?<T_L_PAREN>\()
    |
    (?<T_R_PAREN>\))
    |
    (?<T_SPACE>\s+)
    |
    (?<T_ONE>1)
    |
    (?<T_DIV>/)
    |
    (?<T_UNKNOWN>.)
)
#x
REGEX;

    private const MEASURE_UNIT_REGEX_TEMPLATE = <<<'REGEX'
#
^
(?:
    (?<ratio>\d+(?:\.\d+)?)
    \s+
)?
(?:
    (?<unit_alone>%s)
    |
    (?<metric_prefix>%s)??
    (?<unit>%s)
)
(?<exponent>-?\d+)?
$
#x
REGEX;

    private $regexMeasureUnit;

    public function __construct(array $siUnits, array $nonSiUnits)
    {
        $metricPrefixes = array_map(
            function (MetricPrefix $metricPrefix): string {
                return $metricPrefix->symbol();
            }, MetricPrefix::values()
        );

        usort($metricPrefixes, 'Zlikavac32\UnitsOfMeasure\cmp_string_prefix_desc');
        usort($siUnits, 'Zlikavac32\UnitsOfMeasure\cmp_string_prefix_desc');
        usort($nonSiUnits, 'Zlikavac32\UnitsOfMeasure\cmp_string_prefix_desc');

        $this->regexMeasureUnit =
            sprintf(
                self::MEASURE_UNIT_REGEX_TEMPLATE, implode('|', $nonSiUnits), implode('|', $metricPrefixes),
                implode('|', $siUnits)
            );
    }

    /**
     * @inheritDoc
     */
    public function parse(string $measureUnit): array
    {
        $tokens = $this->tokenize($measureUnit);

        return $this->parseRoot($measureUnit, $tokens);
    }

    private function tokenize(string $measureUnit): TokenStream
    {
        preg_match_all(self::TOKENIZER_REGEX, $measureUnit, $matches, PREG_SET_ORDER);

        $tokens = [];

        foreach ($matches as $match) {
            foreach ($match as $group => $value) {
                if ('' === $value || is_int($group) || 'T_SPACE' === $group) {
                    continue;
                }

                if ('T_UNKNOWN' === $group) {
                    throw new ParseException($measureUnit);
                }

                $tokens[] = [$group, $value];
            }
        }

        return new TokenStream($measureUnit, $tokens);
    }

    /**
     * @return MeasureUnitComponent[]
     */
    private function parseRoot(string $measureUnit, TokenStream $tokens): array
    {
        if ($tokens->hasNext('T_ONE')) {
            $tokens->pop();

            $components = [];

            if (!$tokens->hasNext('T_DIV')) {
                throw new ParseException($measureUnit);
            }
        } else {
            $components = $this->parseGroup($measureUnit, $tokens);

            if ($tokens->hasNext('T_L_PAREN')) {
                $components = array_merge($components, $this->parseParens($measureUnit, $tokens));
            }
        }

        if ($tokens->hasNext('T_DIV')) {
            $tokens->pop();

            if ($tokens->hasNext('T_L_PAREN')) {
                $bottom = $this->parseParens($measureUnit, $tokens);
            } else {
                if ($tokens->hasNext('T_IDEN')) {
                    $bottom = [$this->parseComponent($measureUnit, $tokens->pop())];
                } else {
                    throw new ParseException($measureUnit);
                }
            }

            $components = array_merge(
                $components,
                array_map(
                    function (MeasureUnitComponent $measureUnitComponent): MeasureUnitComponent {
                        return new StaticMeasureUnitComponent(
                            $measureUnitComponent->factor(),
                            $measureUnitComponent->metricPrefix(),
                            $measureUnitComponent->abbrev(),
                            -$measureUnitComponent->exponent()
                        );
                    },
                    $bottom
                )
            );
        }

        $components = array_merge($components, $this->parseGroup($measureUnit, $tokens));

        if (!$tokens->isEmpty()) {
            $components = array_merge($components, $this->parseRoot($measureUnit, $tokens));
        }

        return $components;
    }

    /**
     * @return MeasureUnitComponent[]
     */
    private function parseGroup(string $measureUnit, TokenStream $tokens): array
    {
        $components = [];

        while ($tokens->hasNext('T_IDEN')) {
            $components[] = $this->parseComponent($measureUnit, $tokens->pop());
        }

        return $components;
    }

    private function parseComponent(string $measureUnit, string $value): MeasureUnitComponent
    {
        if (!preg_match($this->regexMeasureUnit, $value, $matches)) {
            throw new ParseException($measureUnit);
        }

        if (!empty($matches['unit_alone'])) {
            return new StaticMeasureUnitComponent(
                new Decimal((float) ($matches['ratio'] ? $matches['ratio'] : 1)),
                MetricPrefix::NONE(), $matches['unit_alone'],
                (int) ($matches['exponent'] ?? 1)
            );
        }

        return new StaticMeasureUnitComponent(
            new Decimal((float) ($matches['ratio'] ? $matches['ratio'] : 1)),
            MetricPrefix::valueOfSymbol($matches['metric_prefix']), $matches['unit'],
            (int) ($matches['exponent'] ?? 1)
        );
    }

    private function parseParens(string $measureUnit, TokenStream $tokens): array
    {
        $tokens->pop();

        $components = $this->parseGroup($measureUnit, $tokens);

        if (!$tokens->hasNext('T_R_PAREN')) {
            throw new ParseException($measureUnit);
        }

        $tokens->pop();

        return $components;
    }
}

/**
 * @internal
 */
final class TokenStream
{

    /**
     * @var array
     */
    private $tokens;
    /**
     * @var int
     */
    private $position;
    /**
     * @var int
     */
    private $count;
    /**
     * @var string
     */
    private $measureUnit;

    public function __construct(string $measureUnit, array $tokens)
    {
        $this->tokens = $tokens;
        $this->position = 0;
        $this->count = count($tokens);
        $this->measureUnit = $measureUnit;
    }

    public function isEmpty(): bool
    {
        return $this->position === $this->count;
    }

    public function pop(): string
    {
        if ($this->isEmpty()) {
            throw new ParseException($this->measureUnit);
        }

        return $this->tokens[$this->position++][1];
    }

    public function hasNext(string $token): bool
    {
        return !$this->isEmpty() && $this->tokens[$this->position][0] === $token;
    }
}
