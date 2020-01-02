<?php

declare(strict_types=1);

namespace Zlikavac32\UnitsOfMeasure;

use LogicException;
use Throwable;

class MethodNotSupportedException extends LogicException
{


    private string $class;

    private string $method;

    public function __construct(string $method, Throwable $previous = null)
    {
        $parts = explode('::', $method);

        if (count($parts) !== 2) {
            throw new LogicException(sprintf('Expected %s to be in format FQN::method', $method));
        }

        $this->class = $parts[0];
        $this->method = $parts[1];

        parent::__construct(
            sprintf('Method %s is not supported', $method), 0, $previous
        );
    }

    public function classFqn(): string
    {
        return $this->class;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function methodFqn(): string
    {
        return $this->class . '::' . $this->method;
    }
}
