<?php
declare(strict_types=1);

namespace D3lph1\Boollet;

use D3lph1\Boollet\Structure\Expression\BinaryExpression;
use D3lph1\Boollet\Structure\Expression\Constant;
use D3lph1\Boollet\Structure\Expression\Expression;
use D3lph1\Boollet\Structure\Expression\Variable;
use D3lph1\Boollet\Structure\Operator\BinaryOperators;

final class ZhegalkinPolynomial
{
    private function __construct()
    {
    }

    public static function calculate(Expression $expr): Expression
    {
        $triangle = self::buildTriangle($expr->toVectorValuedFunction());

        $table = TruthTable::tabulate($expr);
        $rows = $table->getRows();

        $prev = null;

        foreach ($triangle as $i => $triangleRow) {
            if ($triangleRow[0]) {
                if ($prev === null) {
                    $prev = self::buildConjunctives(array_values($rows[$i]), $table->getVariables());
                    continue;
                }

                $prev = new BinaryExpression(
                    self::buildConjunctives(array_values($rows[$i]), $table->getVariables()),
                    BinaryOperators::XOR,
                    $prev
                );
            }
        }

        return $prev;
    }

    private static function buildTriangle(array $vectorValuedFunction): array
    {
        $triangle = [$vectorValuedFunction];

        while (count($vectorValuedFunction) > 1) {
            $vectorValuedFunction = self::doBuildTriangle($vectorValuedFunction);
            $triangle[] = $vectorValuedFunction;
        }

        return $triangle;
    }

    private static function doBuildTriangle(array $vectorValuedFunction): array
    {
        $nextVector = [];

        for ($i = 0; $i < count($vectorValuedFunction) - 1; $i++) {
            // [!] Wrap in brackets because XOR has lower priority than assignment
            $nextVector[] = ($vectorValuedFunction[$i] xor $vectorValuedFunction[$i + 1]);
        }

        return $nextVector;
    }

    /**
     * @param array<bool> $values
     * @param array<Variable> $variables
     */
    private static function buildConjunctives(array $values, array $variables): Expression
    {
        $prev = null;

        foreach ($values as $i => $value) {
            if ($value) {
                if ($prev === null) {
                    $prev = $variables[$i];
                    continue;
                }

                $prev = new BinaryExpression(
                    $variables[$i],
                    BinaryOperators::AND,
                    $prev
                );
            }
        }

        if ($prev === null) {
            return new Constant(true);
        } else {
            return $prev;
        }
    }
}
