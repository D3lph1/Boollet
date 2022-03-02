<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Structure\Expression;

use Stringable;

interface Expression extends Stringable
{
    /**
     * Evaluate an expression and return calculation result
     */
    public function evaluate(array $labelToValue = []): bool;

    public function toVectorValuedFunction(): array;

    /**
     * Get all variables that appearing in the expression itself and in all nested expressions that it contains
     *
     * @return array<Variable>
     */
    public function getAppearingVariables(): array;
}
