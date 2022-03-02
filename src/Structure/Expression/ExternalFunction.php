<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Structure\Expression;

use D3lph1\Boollet\Label\ExpressionLabelPool;

class ExternalFunction extends AbstractExpression
{
    /**
     * @var callable
     */
    private $evalCallback;

    /**
     * @var array<Expression>
     */
    private array $arguments;

    private string $label;

    /**
     * @param array<Variable> $arguments
     */
    public function __construct(callable $evalCallback, array $arguments, ?string $label = null)
    {
        $this->evalCallback = $evalCallback;
        $this->arguments = $arguments;
        $this->label = $label ?? ExpressionLabelPool::getDefaultPool()->getLabelFor($this);
    }

    public function evaluate(array $labelToValue = []): bool
    {
        $arguments = [];

        foreach ($this->arguments as $argument) {
            if (isset($labelToValue[$argument->getLabel()])) {
                $arguments[$argument->getLabel()] = $labelToValue[$argument->getLabel()];
            } else {
                $arguments[$argument->getLabel()] = $argument->evaluate($labelToValue);
            }
        }

        return ($this->evalCallback)(...$arguments);
    }

    /**
     * @return array<Variable>
     */
    public function getAppearingVariables(): array
    {
        return array_unique(array_merge(...array_map(fn(Expression $arg) => $arg->getAppearingVariables(), $this->arguments)));
    }

    public function __toString(): string
    {
        return $this->label . '(' . join(', ', array_map(fn(Expression $arg) => $arg->__toString(), $this->arguments)) . ')';
    }
}
