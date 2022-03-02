<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Structure\Operator;

use D3lph1\Boollet\Structure\Expression\Expression;

enum UnaryOperators implements UnaryOperator
{
    case NOT;

    public function getAliases(): array
    {
        return ['!', '~'];
    }

    public function evaluate(Expression $expr, array $labelToValue = []): bool
    {
        return match ($this) {
            self::NOT => !$expr->evaluate($labelToValue)
        };
    }

    public function revert(bool $val): bool
    {
        return match ($this) {
            self::NOT => !$val
        };
    }

    public function getPrecedence(): int
    {
        return match ($this) {
            self::NOT => 1
        };
    }

    public function toString(): string
    {
        return match ($this) {
            self::NOT => '!'
        };
    }
}
