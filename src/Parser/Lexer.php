<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Parser;

use D3lph1\Boollet\Parser\Reader\InputReader;
use D3lph1\Boollet\Structure\Operator\BinaryOperators;
use D3lph1\Boollet\Structure\Operator\Operator;
use D3lph1\Boollet\Structure\Operator\UnaryOperator;
use D3lph1\Boollet\Structure\Operator\UnaryOperators;

class Lexer
{
    private array $aliasToOperator;

    private ?string $peek = ' ';

    private int $absolutePosition = 0;

    private int $line = 1;

    private int $linePosition = 0;

    /**
     * @param array<string, Operator> $aliasToOperator
     */
    public function __construct(array $aliasToOperator)
    {
        $this->aliasToOperator = $aliasToOperator;
    }

    public static function default(): Lexer
    {
        $aliasToOperator = [];

        self::appendAliasToOperator(BinaryOperators::cases(), $aliasToOperator);
        self::appendAliasToOperator(UnaryOperators::cases(), $aliasToOperator);

        return new Lexer($aliasToOperator);
    }

    private static function appendAliasToOperator(array $cases, array& $aliasToOperator): void
    {
        foreach ($cases as $case) {
            foreach ($case->getAliases() as $alias) {
                $aliasToOperator[$alias] = $case;
            }
        }
    }

    /**
     * @return iterable<Token>
     */
    public function tokenize(InputReader $reader): iterable
    {
        while (true) {
            for (; ; $this->read($reader)) {
                if ($this->peek === ' ' || $this->peek === "\t" || $this->peek === "\r") {
                    continue;
                } else if ($this->peek === "\n") {
                    $this->line++;
                    $this->linePosition = 0;
                } else {
                    break;
                }
            }

            if ($this->peek === null) {
                return;
            }

            switch ($this->peek) {
                // String literal.
                case '(':
                    yield new Token(Token::BRACKET_LEFT, null);
                    $this->read($reader);
                    continue 2;
                case ')':
                    yield new Token(Token::BRACKET_RIGHT, null);
                    $this->read($reader);
                    continue 2;
            }

            if (isset($this->aliasToOperator[$this->peek])) {
                yield new Token(Token::OPERATOR, $this->aliasToOperator[$this->peek]);
                $this->read($reader);
                continue;
            }

            $mbOperator = $this->tryTokenizeOperator($reader);

            if (is_string($mbOperator)) {
                yield new Token(Token::VARIABLE, $mbOperator);
            } elseif ($mbOperator instanceof Operator) {
                yield new Token(Token::OPERATOR, $mbOperator);
            }
        }
    }

    private function read(InputReader $reader): void
    {
        $this->peek = $reader->read();
        $this->absolutePosition++;
        $this->linePosition++;
    }

    private function tryTokenizeOperator(InputReader $reader): Operator|string|null
    {
        $operator = $this->peek;

        $this->read($reader);

        while ($this->peek !== ' ' && $this->peek !== "\t" && $this->peek !== "\r" && $this->peek !== "\n" && $this->peek !== '(' && $this->peek !== ')' && !isset($this->aliasToOperator[$this->peek])) {
            $operator .= $this->peek;
            $this->read($reader);

            if ($this->peek === null) {
                return $operator;
            }
        }

        if (isset($this->aliasToOperator[$operator])) {
            return $this->aliasToOperator[$operator];
        }

        return $operator;
    }
}
