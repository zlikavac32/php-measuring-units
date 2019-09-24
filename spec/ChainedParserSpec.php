<?php

declare(strict_types=1);

namespace spec\Zlikavac32\UnitsOfMeasure;

use LogicException;
use PhpSpec\ObjectBehavior;
use Zlikavac32\UnitsOfMeasure\ChainedParser;
use Zlikavac32\UnitsOfMeasure\MeasureUnitComponent;
use Zlikavac32\UnitsOfMeasure\ParseException;
use Zlikavac32\UnitsOfMeasure\Parser;

class ChainedParserSpec extends ObjectBehavior
{

    public function let(Parser $firstParser, Parser $secondParser): void
    {
        $this->beConstructedWith($firstParser, $secondParser);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ChainedParser::class);
    }

    public function it_should_throw_exception_when_no_parsers_provided(): void
    {
        $this->beConstructedWith();

        $this->shouldThrow(LogicException::class)->duringInstantiation();
    }

    public function it_should_return_results_of_first_successful_parse(
        Parser $firstParser, Parser $secondParser, MeasureUnitComponent $m3
    ): void {
        $firstParser->parse('m3')->willThrow(new ParseException('m3'));
        $secondParser->parse('m3')->willReturn([$m3]);

        $this->parse('m3')->shouldReturn([$m3]);
    }

    public function it_should_throw_exception_when_no_parser_can_parse(Parser $firstParser, Parser $secondParser): void
    {
        $firstParser->parse('m3')->willThrow(new ParseException('m3'));
        $secondParser->parse('m3')->willThrow(new ParseException('m3'));

        $this->shouldThrow(ParseException::class)->duringParse('m3');
    }
}
