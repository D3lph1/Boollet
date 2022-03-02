<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Tests\Parser;

use D3lph1\Boollet\Parser\Lexer;
use D3lph1\Boollet\Parser\Reader\StringInputReader;
use D3lph1\Boollet\Parser\ShuntingYardParser;
use PHPUnit\Framework\TestCase;

class ShuntingYardParserTest extends TestCase
{
    public function test(): void
    {
        $parser = new ShuntingYardParser(Lexer::default());

        $expression = $parser->parse(new StringInputReader('a∨b∧!(a → !c)'));

        self::assertEquals('(a ⋁ (b ⋀ !(a → !c)))', $expression->__toString());
    }
}
