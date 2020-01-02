<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use LogicException;

class ChainedParser implements Parser
{

    /**
     * @var Parser[]
     */
    private array $parsers;

    public function __construct(Parser ...$parsers)
    {
        if (count($parsers) === 0) {
            throw new LogicException('No parsers provided');
        }

        $this->parsers = $parsers;
    }

    /**
     * @inheritDoc
     */
    public function parse(string $measureUnit): array
    {
        foreach ($this->parsers as $parser) {
            try {
                return $parser->parse($measureUnit);
            } catch (ParseException $e) {
                // ignore
            }
        }

        throw new ParseException($measureUnit);
    }
}
