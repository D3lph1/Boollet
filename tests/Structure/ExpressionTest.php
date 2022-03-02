<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Tests\Structure;

use D3lph1\Boollet\Structure\Expression\BinaryExpression;
use D3lph1\Boollet\Structure\Expression\UnaryExpression;
use D3lph1\Boollet\Structure\Expression\Variable;
use D3lph1\Boollet\Structure\Operator\BinaryOperators;
use D3lph1\Boollet\Structure\Operator\UnaryOperators;
use PHPUnit\Framework\TestCase;

class ExpressionTest extends TestCase
{
    public function test(): void
    {
        $expr = new BinaryExpression(
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

        self::assertEquals('(!X → ((Y ⊕ X) ⋀ Z))', $expr->__toString());
        self::assertEquals([ false, false, false, true, true, true, true, true], $expr->toVectorValuedFunction());
    }
}
