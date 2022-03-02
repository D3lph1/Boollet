<?php
declare(strict_types=1);

namespace D3lph1\Boollet;

use Countable;
use D3lph1\Boollet\Structure\Expression\Expression;
use D3lph1\Boollet\Structure\Expression\Variable;
use InvalidArgumentException;
use Stringable;
use texttable;

class TruthTable implements Countable, Stringable
{
    /**
     * @var array<Variable>
     */
    private array $variables;

    /**
     * @var array<string, Variable>
     */
    private array $labelToVariable = [];

    /**
     * @var array<array<bool>>
     */
    private array $variableCombinations;

    /**
     * @var array<bool>
     */
    private array $values;

    private ?string $label;

    /**
     * @param array<Variable> $variables
     * @param array<array<bool>> $variableCombinations
     * @param array<bool> $values
     */
    public function __construct(array $variables, array $variableCombinations, array $values, ?string $label = null)
    {
        if (count($variableCombinations) !== count($values)) {
            throw new InvalidArgumentException('Array arguments $variableCombinations and $values must have the same length.');
        }

        $this->variables = $variables;

        foreach ($variables as $variable) {
            $this->labelToVariable[$variable->getLabel()] = $variable;
        }

        $this->variableCombinations = $variableCombinations;
        $this->values = $values;
        $this->label = $label;
    }

    public static function tabulate(Expression $expression): self
    {
        $variables = $expression->getAppearingVariables();

        $labels = [];

        $combine = [];
        foreach ($variables as $variable) {
            $combine[] = [false, true];

            $labels[] = $variable->getLabel();
        }

        $variableCombinations = [];
        $values = [];
        foreach (self::combinations($combine) as $combination) {
            $variableCombinations[] = $combination;

            $labelToVariableSetup = [];

            foreach ($combination as $combinationItemIndex => $combinationItem) {
                $label = $labels[$combinationItemIndex];
                $labelToVariableSetup[$label] = $combinationItem;
            }

            $values[] = $expression->evaluate($labelToVariableSetup);
        }

        return new self(array_values($variables), $variableCombinations, $values, $expression->__toString());
    }

    private static function combinations(array $arrays): array
    {
        if (count($arrays) === 1) {
            $newArrays = [];
            foreach ($arrays as $array) {
                foreach ($array as $item) {
                    $newArrays[] = [$item];
                }
            }

            return $newArrays;
        }

        return self::doCombinations($arrays, 0);
    }

    private static function doCombinations(array $arrays, int $i): array
    {
        if (!isset($arrays[$i])) {
            return [];
        }

        if ($i == count($arrays) - 1) {
            return $arrays[$i];
        }

        // get combinations from subsequent arrays
        $tmp = self::doCombinations($arrays, $i + 1);

        $result = [];

        // concat each array from tmp with each element from $arrays[$i]
        foreach ($arrays[$i] as $v) {
            foreach ($tmp as $t) {
                $result[] = is_array($t) ? array_merge([$v], $t) : [$v, $t];
            }
        }

        return $result;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getRowsWithNegativeExpressionValuesOnly(): array
    {
        $combinations = [];

        foreach ($this->variableCombinations as $i => $variableCombination) {
            if (!$this->values[$i]) {
                $combination = [];

                foreach ($variableCombination as $j => $each) {
                    $combination[$this->variables[$j]->getLabel()] = $each;
                }

                $combinations[$i] = $combination;
            }
        }

        return $combinations;
    }

    public function getRowsWithPositiveExpressionValuesOnly(): array
    {
        $combinations = [];

        foreach ($this->variableCombinations as $i => $variableCombination) {
            if ($this->values[$i]) {
                $combination = [];

                foreach ($variableCombination as $j => $each) {
                    $combination[$this->variables[$j]->getLabel()] = $each;
                }

                $combinations[$i] = $combination;
            }
        }

        return $combinations;
    }

    public function count(): int
    {
        return count($this->variableCombinations);
    }

    public function __toString(): string
    {
        $data = [];

        foreach ($this->variableCombinations as $j => $variableCombination) {
            $row = [];

            foreach ($this->variables as $i => $variable) {
                $row[$variable->getLabel()] = $variableCombination[$i] ? '1' : '0';
            }

            $row[$this->label ?? 'f()'] = $this->values[$j] ? '1' : '0';

            $data[] = $row;
        }

        return texttable::table($data);
    }

    /**
     * @return array<Variable>
     */
    public function getAppearingVariables(): array
    {
        return $this->variables;
    }

    public function findVariable(string $label): ?Variable
    {
        if (isset($this->labelToVariable[$label])) {
            return $this->labelToVariable[$label];
        }

        return null;
    }

    public function getRows(): array
    {
        return $this->variableCombinations;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
