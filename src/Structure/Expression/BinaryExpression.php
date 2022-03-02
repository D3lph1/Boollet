<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Structure\Expression;

use D3lph1\Boollet\Structure\Operator\BinaryOperator;

class BinaryExpression extends AbstractExpression
{
    /**
     * @var Expression Left operand of the expression
     */
    private Expression $left;

    /**
     * @var BinaryOperator The operator of the expression
     */
    private BinaryOperator $operator;

    /**
     * @var Expression Right operand of the expression
     */
    private Expression $right;

    public function __construct(Expression $left, BinaryOperator $operator, Expression $right)
    {
        $this->left = $left;
        $this->operator = $operator;
        $this->right = $right;
    }

    public function getLeft(): Expression
    {
        return $this->left;
    }

    public function getOperator(): BinaryOperator
    {
        return $this->operator;
    }

    public function getRight(): Expression
    {
        return $this->right;
    }

    public function evaluate(array $labelToValue = []): bool
    {
        return $this->operator->evaluate($this->left, $this->right, $labelToValue);
    }

    /**
     * @return array<Variable>
     */
    public function getAppearingVariables(): array
    {
        return array_unique(array_merge($this->left->getAppearingVariables(), $this->right->getAppearingVariables()));
    }

    public function __toString(): string
    {
        return '(' . $this->left->__toString() . ' ' . $this->operator->toString() . ' ' . $this->right->__toString() . ')';
    }
}
