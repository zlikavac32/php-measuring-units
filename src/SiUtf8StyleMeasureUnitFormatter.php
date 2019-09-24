<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

class SiUtf8StyleMeasureUnitFormatter implements MeasureUnitFormatter
{

    public function format(MeasureUnit $measureUnit): string
    {
        $components = [];

        foreach ($measureUnit->components() as $component) {
            $factor = $component->factor();

            $str = '';

            if (!$factor->equalsTo(1)) {
                $str .= (string) $factor->asFloat() . ' ';
            }

            if ($component->metricPrefix() === MetricPrefix::MICRO()) {
                $str .= 'μ';
            } else if ($component->metricPrefix() !== MetricPrefix::NONE()) {
                $str .= $component->metricPrefix()->symbol();
            }

            $str .= $component->abbrev();

            $exponent = $component->exponent();

            if ($exponent !== 1) {
                $str .= $this->exponentToUtf8String($exponent);
            }

            $components[] = $str;
        }

        $factor = $measureUnit->factor();

        if ($factor->equalsTo(1)) {
            return implode('⋅', $components);
        } else if (empty($components)) {
            return (string) $factor->asFloat();
        }

        return (string) $factor->asFloat() . ' ' . implode('⋅', $components);
    }

    private function exponentToUtf8String(int $exponent): string
    {
        $str = '';

        if ($exponent < 0) {
            $exponent = -$exponent;
            $str .= '⁻';
        }

        $tmp = '';

        while ($exponent > 0) {
            $tmp = ['⁰', '¹', '²', '³', '⁴', '⁵', '⁶', '⁷', '⁸', '⁹'][$exponent % 10] . $tmp;

            $exponent = (int) ($exponent / 10);
        }

        return $str . $tmp;
    }
}
