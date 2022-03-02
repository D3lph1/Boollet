<?php
declare(strict_types=1);

namespace D3lph1\Boollet\SAT;

use D3lph1\Boollet\Constraints\Constraints;
use D3lph1\Boollet\Structure\Expression\Expression;

interface ConstraintsAwareSATSolver extends SATSolver
{
    /**
     * @param array<string> $variables List of variable labels regarding which the SAT needs to be resolved
     * @return array<array<string, bool>> List of all possible solutions
     */
    public function findAllPossibleSolutionsWithConstraints(Expression $expr, array $variables, Constraints $constraints): array;

    /**
     * @param array<string> $variables List of variable labels regarding which the SAT needs to be resolved
     * @return array<array<string, bool>> First N found solutions
     */
    public function findFirstNSolutionsWithConstraints(int $n, Expression $expr, array $variables, Constraints $constraints): array;
}
