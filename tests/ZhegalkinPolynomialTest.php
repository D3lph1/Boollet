<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Tests;

use D3lph1\Boollet\Structure\Expression\BinaryExpression;
use D3lph1\Boollet\Structure\Expression\UnaryExpression;
use D3lph1\Boollet\Structure\Expression\Variable;
use D3lph1\Boollet\Structure\Operator\BinaryOperators;
use D3lph1\Boollet\Structure\Operator\UnaryOperators;
use D3lph1\Boollet\ZhegalkinPolynomial;
use PHPUnit\Framework\TestCase;

class ZhegalkinPolynomialTest extends TestCase
{
    public function test(): void
    {
        $x = new Variable(label: 'X');
        $y = new Variable(label: 'Y');
        $z = new Variable(label: 'Z');

        $expr = new BinaryExpression(
            new UnaryExpression(UnaryOperators::NOT, $x),
            BinaryOperators::IMPLIES,
            new BinaryExpression(
                new BinaryExpression(
                    new UnaryExpression(UnaryOperators::NOT, $y),
                    BinaryOperators::XOR,
                    $x
                ),
                BinaryOperators::AND,
                new UnaryExpression(UnaryOperators::NOT, $z)
            )
        );

        self::assertEquals('((Z ⋀ (Y ⋀ X)) ⊕ ((Y ⋀ X) ⊕ ((Z ⋀ X) ⊕ ((Z ⋀ Y) ⊕ (Y ⊕ (Z ⊕ 1))))))', ZhegalkinPolynomial::calculate($expr)->__toString());
    }
}
