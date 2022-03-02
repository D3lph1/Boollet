<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Tests\Parser;

use D3lph1\Boollet\Parser\Lexer;
use D3lph1\Boollet\Parser\Reader\StringInputReader;
use D3lph1\Boollet\Parser\Token;
use D3lph1\Boollet\Structure\Operator\BinaryOperators;
use D3lph1\Boollet\Structure\Operator\UnaryOperators;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    public function test(): void
    {
        $lexer = Lexer::default();

        /** @var array<Token> $tokens */
        $tokens = iterator_to_array($lexer->tokenize(new StringInputReader('a∨b∧!(a → !c)')));

        self::assertEquals(11, count($tokens));

        // a
        self::assertEquals(Token::VARIABLE, $tokens[0]->getType());
        self::assertEquals('a', $tokens[0]->getPayload());

        // ∨
        self::assertEquals(Token::OPERATOR, $tokens[1]->getType());
        self::assertEquals(BinaryOperators::OR, $tokens[1]->getPayload());

        // b
        self::assertEquals(Token::VARIABLE, $tokens[2]->getType());
        self::assertEquals('b', $tokens[2]->getPayload());

        // ∧
        self::assertEquals(Token::OPERATOR, $tokens[3]->getType());
        self::assertEquals(BinaryOperators::AND, $tokens[3]->getPayload());

        // !
        self::assertEquals(Token::OPERATOR, $tokens[4]->getType());
        self::assertEquals(UnaryOperators::NOT, $tokens[4]->getPayload());

        // (
        self::assertEquals(Token::BRACKET_LEFT, $tokens[5]->getType());
        self::assertEquals(null, $tokens[5]->getPayload());

        // a
        self::assertEquals(Token::VARIABLE, $tokens[6]->getType());
        self::assertEquals('a', $tokens[6]->getPayload());

        // →
        self::assertEquals(Token::OPERATOR, $tokens[7]->getType());
        self::assertEquals(BinaryOperators::IMPLIES, $tokens[7]->getPayload());

        // !
        self::assertEquals(Token::OPERATOR, $tokens[8]->getType());
        self::assertEquals(UnaryOperators::NOT, $tokens[8]->getPayload());

        // c
        self::assertEquals(Token::VARIABLE, $tokens[9]->getType());
        self::assertEquals('c', $tokens[9]->getPayload());

        // )
        self::assertEquals(Token::BRACKET_RIGHT, $tokens[10]->getType());
        self::assertEquals(null, $tokens[10]->getPayload());
    }
}
