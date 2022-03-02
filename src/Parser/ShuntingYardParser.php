<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Parser;

use D3lph1\Boollet\MutableValueSupplier;
use D3lph1\Boollet\Parser\Reader\InputReader;
use D3lph1\Boollet\Structure\Expression\BinaryExpression;
use D3lph1\Boollet\Structure\Expression\Expression;
use D3lph1\Boollet\Structure\Expression\UnaryExpression;
use D3lph1\Boollet\Structure\Expression\Variable;
use D3lph1\Boollet\Structure\Operator\BinaryOperator;
use D3lph1\Boollet\ValueBinder;
use SplQueue;
use SplStack;

class ShuntingYardParser implements Parser
{
    private Lexer $lexer;

    private ?ValueBinder $valueBinder = null;

    /**
     * @var SplQueue<Token>
     */
    private SplQueue $outputQueue;

    /**
     * @var SplStack<Token>
     */
    private SplStack $operatorStack;

    private array $labelToVariable;

    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }

    public function setValueBinder(?ValueBinder $valueBinder): void
    {
        $this->valueBinder = $valueBinder;
    }

    public function parse(InputReader $input): Expression
    {
        $this->outputQueue = new SplQueue();
        $this->operatorStack = new SplStack();
        $this->labelToVariable = [];

        foreach ($this->lexer->tokenize($input) as $token) {
            switch ($token->getType()) {
                case Token::VARIABLE:
                    $this->outputQueue->push($token);
                    break;
                case Token::OPERATOR:
                    $op1 = $token;

                    while (
                        count($this->operatorStack) !== 0
                        && $this->operatorStack->top()->getType() === Token::OPERATOR
                        && $this->operatorStack->top()->getPayload()->getPrecedence() <= $op1->getPayload()->getPrecedence()
                    ) {
                        $this->ensureStackNonEmpty($this->operatorStack);

                        $this->outputQueue->push($this->operatorStack->pop());
                    }

                    $this->operatorStack->push($op1);
                    break;
                case Token::BRACKET_LEFT:
                    $this->operatorStack->push($token);
                    break;
                case Token::BRACKET_RIGHT:
                    while (count($this->operatorStack) !== 0 && $this->operatorStack->top()->getType() !== Token::BRACKET_LEFT) {
                        $this->ensureStackNonEmpty($this->operatorStack);
                        $this->outputQueue->push($this->operatorStack->pop());
                    }

                    if (count($this->operatorStack) === 0) {
                        throw ParserException::missingLeftBracket();
                    }

                    $this->ensureStackNonEmpty($this->operatorStack);

                    $this->operatorStack->pop();
                    break;
            }
        }

        while (count($this->operatorStack) !== 0) {
            if ($this->operatorStack->top()->getType() === Token::BRACKET_LEFT) {
                throw ParserException::missingRightBracket();
            }

            $this->outputQueue->push($this->operatorStack->pop());
        }

        return $this->buildExpression();
    }

    /**
     * @throws ParserException
     */
    private function buildExpression(): Expression
    {
        $stack = new SplStack();

        foreach ($this->outputQueue as $token) {
            if ($token->getType() === Token::VARIABLE) {
                $stack->push($this->createLeaf($token));
            } else if ($token->getType() === Token::OPERATOR) {
                $this->ensureStackNonEmpty($stack);
                if ($token->getPayload() instanceof BinaryOperator) {
                    $right = $stack->pop();
                    $this->ensureStackNonEmpty($stack);
                    $left = $stack->pop();

                    $node = new BinaryExpression($left, $token->getPayload(), $right);
                } else {
                    $node = new UnaryExpression($token->getPayload(), $stack->pop());
                }


                $stack->push($node);
            }
        }

        if (count($stack) !== 1) {
            throw new ParserException();
        }

        return $stack->pop();
    }

    private function createLeaf(Token $token): Variable
    {
        if (isset($this->labelToVariable[$token->getPayload()])) {
            return $this->labelToVariable[$token->getPayload()];
        }

        $leaf = new Variable(null, $token->getPayload());
        $this->valueBinder?->bind($leaf);
        $this->labelToVariable[$token->getPayload()] = $leaf;

        return $leaf;
    }

    /**
     * @throws ParserException
     */
    private function ensureStackNonEmpty(SplStack $stack): void
    {
        if (count($stack) === 0) {
            throw new ParserException();
        }
    }
}
