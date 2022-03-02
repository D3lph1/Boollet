<?php
declare(strict_types=1);

namespace D3lph1\Boollet\SAT;

use D3lph1\Boollet\NormalForm\NotACompleteConjunctiveNormalFormException;
use D3lph1\Boollet\NormalForm\NotANormalFormException;
use D3lph1\Boollet\Structure\Expression\Expression;
use D3lph1\Boollet\Structure\Operator\BinaryOperator;
use D3lph1\Boollet\Structure\Operator\BinaryOperators;
use Throwable;

class CompleteConjunctiveNormalFormUNSATSolver extends AbstractCompleteNormalFormSATSolver
{
    protected function getTargetValue(): bool
    {
        return false;
    }

    protected function getNonTerminalSubExpressionOperator(): BinaryOperator
    {
        return BinaryOperators::AND;
    }

    protected function getTerminalSubExpressionOperator(): BinaryOperator
    {
        return BinaryOperators::OR;
    }

    protected function createNotACompleteNormalFormException(Expression $expr, ?Throwable $prev = null): NotANormalFormException
    {
        return new NotACompleteConjunctiveNormalFormException($expr, $prev);
    }
}
