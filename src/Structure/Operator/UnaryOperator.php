<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Structure\Operator;

use D3lph1\Boollet\Structure\Expression\Expression;

interface UnaryOperator extends Operator
{
    public function evaluate(Expression $expr, array $labelToValue = []): bool;

    public function revert(bool $val): bool;
}
