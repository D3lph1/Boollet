<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Structure\Operator;

use D3lph1\Boollet\Structure\Expression\Expression;

interface BinaryOperator extends Operator
{
    public function evaluate(Expression $left, Expression $right, array $labelToValue = []): bool;
}
