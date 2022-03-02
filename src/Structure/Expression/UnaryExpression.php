<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Structure\Expression;

use D3lph1\Boollet\Structure\Operator\UnaryOperator;

class UnaryExpression extends AbstractExpression
{
    private UnaryOperator $operator;

    private Expression $expr;

    public function __construct(UnaryOperator $operator, Expression $expr)
    {
        $this->operator = $operator;
        $this->expr = $expr;
    }

    public function getOperator(): UnaryOperator
    {
        return $this->operator;
    }

    public function getExpr(): Expression
    {
        return $this->expr;
    }

    public function evaluate(array $labelToValue = []): bool
    {
        return $this->operator->evaluate($this->expr, $labelToValue);
    }

    public function getAppearingVariables(): array
    {
        return array_unique($this->expr->getAppearingVariables());
    }

    public function __toString(): string
    {
        return $this->operator->toString() . $this->expr->__toString();
    }
}
