<?php
declare(strict_types=1);

namespace D3lph1\Boollet\SAT;

use D3lph1\Boollet\Constraints\Constraints;
use D3lph1\Boollet\NormalForm\NotACompleteDisjunctiveNormalFormException;
use D3lph1\Boollet\NormalForm\NotANormalFormException;
use D3lph1\Boollet\Structure\Expression\BinaryExpression;
use D3lph1\Boollet\Structure\Expression\Expression;
use D3lph1\Boollet\Structure\Expression\UnaryExpression;
use D3lph1\Boollet\Structure\Expression\UnexpectedExpressionTypeException;
use D3lph1\Boollet\Structure\Expression\Variable;
use D3lph1\Boollet\Structure\Operator\BinaryOperator;
use InvalidArgumentException;
use Throwable;

abstract class AbstractCompleteNormalFormSATSolver extends AbstractSATSolver implements ConstraintsAwareSATSolver
{
    protected ?Constraints $constraints = null;

    protected abstract function getTargetValue(): bool;

    protected abstract function getNonTerminalSubExpressionOperator(): BinaryOperator;

    protected abstract function getTerminalSubExpressionOperator(): BinaryOperator;

    protected abstract function createNotACompleteNormalFormException(Expression $expr, ?Throwable $prev = null): NotANormalFormException;

    public function findAllPossibleSolutions(Expression $expr, array $variables): array
    {
        $this->constraints = null;

        return $this->solve(null, $expr, $this->convertLabelsForFasterLookup($variables));
    }

    public function findFirstNSolutions(int $n, Expression $expr, array $variables): array
    {
        $this->constraints = null;

        return $this->solve($n, $expr, $this->convertLabelsForFasterLookup($variables));
    }

    public function findAllPossibleSolutionsWithConstraints(Expression $expr, array $variables, Constraints $constraints): array
    {
        $this->constraints = $constraints;

        return $this->solve(null, $expr, $this->convertLabelsForFasterLookup($variables));
    }

    public function findFirstNSolutionsWithConstraints(int $n, Expression $expr, array $variables, Constraints $constraints): array
    {
        $this->constraints = $constraints;

        return $this->solve($n, $expr, $this->convertLabelsForFasterLookup($variables));
    }

    protected function solve(?int $n, Expression $expr, array $variables): array
    {
        try {
            if ($expr instanceof Variable) {
                if (count($variables) !== 1 || $expr->getLabel() !== array_key_first($variables)) {
                    throw new InvalidArgumentException('Invalid input variables. Expected only one "' . array_key_first($variables) . '" variable');
                }

                return [array_key_first($variables) => $this->getTargetValue()];
            }

            if ($expr instanceof UnaryExpression) {
                $appearingVariables = $expr->getAppearingVariables();
                if (count($appearingVariables) !== 1) {
                    throw $this->createNotACompleteNormalFormException($expr);
                }

                $appearingVariable = reset($appearingVariables);

                if (count($variables) !== 1 || $appearingVariable->getLabel() !== array_key_first($variables)) {
                    throw new InvalidArgumentException('Invalid input variables. Expected only one "' . array_key_first($variables) . '" variable');
                }

                return [array_key_first($variables) => $expr->getOperator()->revert($this->getTargetValue())];
            }

            return $this->doSolve($n, $expr, $this->convertLabelsForFasterLookup($variables), $expr);
        } catch (UnexpectedExpressionTypeException $e) {
            throw $this->createNotACompleteNormalFormException($expr, $e);
        }
    }

    private function doSolve(?int $n, Expression $expr, array $variables, Expression $sourceExpr): array
    {
        $allSolutions = [];

        if ($expr instanceof BinaryExpression) {
            if ($expr->getOperator() === $this->getNonTerminalSubExpressionOperator()) {
                $mbSolution = $this->solveConjunctives($expr->getLeft(), $variables, $sourceExpr);
                if ($mbSolution !== null) {
                    $allSolutions[] = $mbSolution;
                }

                if (count($allSolutions) === $n) {
                    return $allSolutions;
                }

                $allSolutions = [...$allSolutions, ...$this->doSolve($n === null ? null : $n - count($allSolutions), $expr->getRight(), $variables, $sourceExpr)];
            } else if ($expr->getOperator() === $this->getTerminalSubExpressionOperator()) {
                $mbSolution = $this->solveConjunctives($expr, $variables, $sourceExpr);
                if ($mbSolution !== null) {
                    $allSolutions[] = $mbSolution;
                }
            } else {
                throw $this->createNotACompleteNormalFormException($sourceExpr);
            }
        }

        return $allSolutions;
    }

    private function solveConjunctives(Expression $expr, array $variables, Expression $sourceExpr): ?array
    {
        $processed = [];
        $termsToSolve = [];

        $flat = self::flattenConjunctives($expr);

        /** @var Variable|UnaryExpression $conjunctive */
        foreach ($flat as $conjunctive) {
            $variable = $conjunctive->getAppearingVariables()[0];
            $label = $variable->getLabel();

            if (isset($processed[$label])) {
                dd($label . ' already processed. Not a DNF.');
            }

            if (isset($variables[$label])) {
                $termsToSolve[] = $conjunctive;
                unset($variables[$label]);
                $processed[$label] = true;
            } else {
                if (!$this->variableTakeValue($conjunctive, $this->getTargetValue())) {
                    return null;
                }
            }
        }

        if (count($variables) !== 0) {
            throw new InvalidArgumentException("Invalid input variables");
        }

        if (count($termsToSolve) === 0) {
            throw $this->createNotACompleteNormalFormException($sourceExpr);
        }

        return $this->equation($termsToSolve);
    }

    private function equation(array $vars): ?array
    {
        $solution = [];

        foreach ($vars as $var) {
            if ($var instanceof Variable) {
                $solution[$var->getLabel()] = $this->getTargetValue();
            } elseif ($var instanceof UnaryExpression) {
                $inner = $var->getAppearingVariables()[0];
                $solution[$inner->getLabel()] = $var->getOperator()->revert($this->getTargetValue());
            } else {
                throw new UnexpectedExpressionTypeException([
                    Variable::class,
                    UnaryExpression::class
                ], $var);
            }
        }

        if ($this->constraints === null) {
            return $solution;
        }

        if ($this->constraints->isSatisfy($solution)) {
            return $solution;
        }

        return null;
    }

    private function variableTakeValue(Expression $expr, bool $value): bool
    {
        if ($expr instanceof Variable || $expr instanceof UnaryExpression) {
            return $expr->evaluate() === $value;
        } else {
            throw new UnexpectedExpressionTypeException([
                Variable::class,
                UnaryExpression::class
            ], $expr);
        }
    }

    private function flattenConjunctives(Expression $expr): array
    {
        if ($expr instanceof Variable) {
            return [$expr];
        }

        if ($expr instanceof UnaryExpression) {
            return [$expr->getExpr()];
        }

        if ($expr instanceof BinaryExpression) {
            $return = [];

            if ($expr->getLeft() instanceof Variable) {
                $return = [$expr->getLeft()];
            } elseif ($expr->getLeft() instanceof UnaryExpression) {
                $return = [$expr->getLeft()];
            } elseif ($expr->getLeft() instanceof BinaryExpression) {
                $return = [...$this->flattenConjunctives($expr->getLeft())];
            }

            if ($expr->getRight() instanceof Variable) {
                $return = [...$return, $expr->getRight()];
            } elseif ($expr->getRight() instanceof UnaryExpression) {
                $return = [...$return, $expr->getRight()];
            } elseif ($expr->getRight() instanceof BinaryExpression) {
                $return = [...$return, ...$this->flattenConjunctives($expr->getRight())];
            }

            return $return;
        }

        throw new UnexpectedExpressionTypeException([
            Variable::class,
            UnaryExpression::class,
            BinaryExpression::class
        ], $expr);
    }
}
