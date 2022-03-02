<?php
declare(strict_types=1);

namespace D3lph1\Boollet\NormalForm;

use D3lph1\Boollet\Structure\Expression\Expression;
use RuntimeException;
use Throwable;

class NotANormalFormException extends RuntimeException
{
    private Expression $expr;

    public function __construct(string $message, Expression $expr, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->expr = $expr;
    }

    public function getExpression(): Expression
    {
        return $this->expr;
    }
}
