<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Structure\Expression;

use D3lph1\Boollet\TruthTable;

abstract class AbstractExpression implements Expression
{
    public function toVectorValuedFunction(): array
    {
        return TruthTable::tabulate($this)->getValues();
    }
}
