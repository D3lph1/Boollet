<?php
declare(strict_types=1);

namespace D3lph1\Boollet;

use D3lph1\Boollet\Structure\Expression\Variable;

class ValueBinder
{
    /**
     * @var array<string, Variable>
     */
    private array $labelToVariable = [];

    public function bind(Variable $variable): self
    {
        $this->labelToVariable[$variable->getLabel()] = $variable;

        return $this;
    }

    public function bindAll(array $variables): self
    {
        foreach ($variables as $variable) {
            $this->bind($variable);
        }

        return $this;
    }

    /**
     * @param array<string, bool> $labelToValue
     */
    public function set(array $labelToValue): self
    {
        foreach ($labelToValue as $label => $value) {
            $variable = $this->labelToVariable[$label];
            $variable->set($value);
        }

        return $this;
    }
}
