<?php
declare(strict_types=1);

namespace D3lph1\Boollet\SAT;

abstract class AbstractSATSolver implements SATSolver
{
    /**
     * @param array<string> $variables
     * @return array<string, string>
     */
    protected function convertLabelsForFasterLookup(array $variables): array
    {
        $converted = [];
        foreach ($variables as $variable) {
            $converted[$variable] = $variable;
        }

        return $converted;
    }
}
