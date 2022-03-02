<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Tests\NormalForm;

use D3lph1\Boollet\NormalForm\NormalForms;
use D3lph1\Boollet\Structure\Expression\BinaryExpression;
use D3lph1\Boollet\Structure\Expression\Expression;
use D3lph1\Boollet\Structure\Expression\UnaryExpression;
use D3lph1\Boollet\Structure\Expression\Variable;
use D3lph1\Boollet\Structure\Operator\BinaryOperators;
use D3lph1\Boollet\Structure\Operator\UnaryOperators;
use PHPUnit\Framework\TestCase;

class NormalFormsTest extends TestCase
{
    public function testCalculateCompleteConjunctive(): void
    {
        $expr = $this->createExpression();
        $ccnf = NormalForms::calculateCompleteConjunctive($expr);

        self::assertEquals('((X ⋁ (Y ⋁ Z)) ⋀ ((X ⋁ (Y ⋁ !Z)) ⋀ (X ⋁ (!Y ⋁ Z))))', $ccnf->__toString());
    }

    public function testCalculateCompleteDisjunctive(): void
    {
        $expr = $this->createExpression();
        $cdnf = NormalForms::calculateCompleteDisjunctive($expr);

        self::assertEquals('((!X ⋀ (Y ⋀ Z)) ⋁ ((X ⋀ (!Y ⋀ !Z)) ⋁ ((X ⋀ (!Y ⋀ Z)) ⋁ ((X ⋀ (Y ⋀ !Z)) ⋁ (X ⋀ (Y ⋀ Z))))))', $cdnf->__toString());
    }

    private function createExpression(): Expression
    {
        return new BinaryExpression(
            new UnaryExpression(UnaryOperators::NOT, new Variable(label: 'X')),
            BinaryOperators::IMPLIES,
            new BinaryExpression(
                new BinaryExpression(
                    new Variable(label: 'Y'),
                    BinaryOperators::XOR,
                    new Variable(label: 'X')
                ),
                BinaryOperators::AND,
                new Variable(label: 'Z')
            )
        );
    }
}
