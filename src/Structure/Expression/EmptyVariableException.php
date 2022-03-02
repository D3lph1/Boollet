<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Structure\Expression;

use RuntimeException;

class EmptyVariableException extends RuntimeException
{
    public function __construct(string $variableLabel)
    {
        parent::__construct("Attempted to get value of empty variable \"$variableLabel\". It's expected that variable \"$variableLabel\" bind to a value, but it is empty.");
    }
}
