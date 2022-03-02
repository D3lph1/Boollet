<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Structure\Expression;

class Constant extends AbstractExpression
{
    private bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function evaluate(array $labelToValue = []): bool
    {
        return $this->value;
    }

    public function getAppearingVariables(): array
    {
        return [];
    }

    public function __toString(): string
    {
        return $this->value ? '1' : '0';
    }
}
