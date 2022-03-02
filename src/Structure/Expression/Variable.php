<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Structure\Expression;

use D3lph1\Boollet\Label\ExpressionLabelPool;

class Variable extends AbstractExpression
{
    private ?bool $value;

    private string $label;

    public function __construct(?bool $initialValue = null, ?string $label = null)
    {
        $this->value = $initialValue;
        $this->label = $label ?? ExpressionLabelPool::getDefaultPool()->getLabelFor($this);
    }

    public function set(bool $value): void
    {
        $this->value = $value;
    }

    public function evaluate(array $labelToValue = []): bool
    {
        if (isset($labelToValue[$this->label])) {
            return $labelToValue[$this->label];
        }

        if ($this->value === null) {
            throw new EmptyVariableException($this->label);
        }

        return $this->value;
    }

    public function getAppearingVariables(): array
    {
        return [$this];
    }

    public function __toString(): string
    {
        return $this->label;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function clone(): Variable
    {
        return new Variable($this->value, $this->label);
    }
}
