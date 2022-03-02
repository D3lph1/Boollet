<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Structure\Expression;

use RuntimeException;

class UnexpectedExpressionTypeException extends RuntimeException
{
    /**
     * @param array<class-string> $expected
     * @param Expression $given
     */
    public function __construct(array $expected, Expression $given)
    {
        $msg = 'Expected expression of the following type(s): ' . join(', ', $expected) . '; but ' . get_class($given) . ' given.';
        parent::__construct($msg);
    }
}
