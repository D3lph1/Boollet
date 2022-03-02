<?php
declare(strict_types=1);

namespace D3lph1\Boollet\NormalForm;

use D3lph1\Boollet\Structure\Expression\BinaryExpression;
use D3lph1\Boollet\Structure\Expression\Expression;
use D3lph1\Boollet\Structure\Expression\UnaryExpression;
use D3lph1\Boollet\Structure\Operator\BinaryOperator;
use D3lph1\Boollet\Structure\Operator\BinaryOperators;
use D3lph1\Boollet\Structure\Operator\UnaryOperators;
use D3lph1\Boollet\TruthTable;

class NormalForms
{
    private function __construct()
    {
    }

    public static function calculateCompleteConjunctive(Expression $expr): Expression
    {
        return self::calculateComplete(
            $expr,
            fn(TruthTable $table) => $table->getRowsWithNegativeExpressionValuesOnly(),
            BinaryOperators::OR,
            BinaryOperators::AND,
            fn(bool $val) => $val
        );
    }

    public static function calculateCompleteDisjunctive(Expression $expr): Expression
    {
        return self::calculateComplete(
            $expr,
            fn(TruthTable $table) => $table->getRowsWithPositiveExpressionValuesOnly(),
            BinaryOperators::AND,
            BinaryOperators::OR,
            fn(bool $val) => !$val
        );
    }

    private static function calculateComplete(
        Expression $expr,
        callable $getRows,
        BinaryOperator $innerOperator,
        BinaryOperator $outerOperator,
        callable $valMapper
    ): Expression
    {
        $table = TruthTable::tabulate($expr);

        $inners = [];
        foreach ($getRows($table) as $row) {
            $inners[] = self::buildInnerExpression($row, $innerOperator, $table, $valMapper);
        }

        return self::buildOuterExpression($inners, $outerOperator);
    }

    private static function buildInnerExpression(array $row, BinaryOperator $operator, TruthTable $table, callable $valMapper): Expression
    {
        $label = array_key_first($row);
        $val = $row[$label];
        $var = $table->findVariable($label);

        if (count($row) === 1) {
            return $valMapper($val) ? new UnaryExpression(UnaryOperators::NOT, $var) : $var;
        }

        unset($row[$label]);

        return new BinaryExpression(
            $valMapper($val) ? new UnaryExpression(UnaryOperators::NOT, $var) : $var,
            $operator,
            self::buildInnerExpression($row, $operator, $table, $valMapper)
        );
    }

    private static function buildOuterExpression(array $row, BinaryOperator $operator): Expression
    {
        $key = array_key_first($row);
        $expr = $row[$key];

        if (count($row) === 1) {
            return $expr;
        }

        unset($row[$key]);

        return new BinaryExpression(
            $expr,
            $operator,
            self::buildOuterExpression($row, $operator)
        );
    }
}
