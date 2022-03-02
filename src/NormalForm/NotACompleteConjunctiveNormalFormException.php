<?php
declare(strict_types=1);

namespace D3lph1\Boollet\NormalForm;

use D3lph1\Boollet\Structure\Expression\Expression;

class NotACompleteConjunctiveNormalFormException extends NotANormalFormException
{
    public function __construct(Expression $expr, ?\Throwable $previous = null)
    {
        parent::__construct("Expected expression in complete conjunctive normal form", $expr, $previous);
    }
}
