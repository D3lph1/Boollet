<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Structure\Operator;

use D3lph1\Boollet\Structure\Expression\Expression;

enum BinaryOperators implements BinaryOperator
{
    case OR;

    case AND;

    case XOR;

    case IMPLIES;

    // Exclusive AND (XAND)
    case EQ;

    // Peirce's arrow
    case NOR;

    // Sheffer's Stroke
    case NAND;

    public function getAliases(): array
    {
        return match ($this) {
            self::OR => [
                '∨', '⋁', '+'
            ],
            self::AND => [
                '∧', '⋀', '&', '•'
            ],
            self::XOR => [
                '⊻', '⊕'
            ],
            self::IMPLIES => [
                '→', '⇒'
            ],
            self::EQ => [
                '≡', '↔', '⇔'
            ],
            self::NOR => [
                '⭣'
            ],
            self::NAND => [
                '|'
            ]
        };
    }

    public function evaluate(Expression $left, Expression $right, array $labelToValue = []): bool
    {
        return match ($this) {
            self::OR => $left->evaluate($labelToValue) || $right->evaluate($labelToValue),
            self::AND => $left->evaluate($labelToValue) && $right->evaluate($labelToValue),
            self::XOR => $left->evaluate($labelToValue) xor $right->evaluate($labelToValue),
            self::IMPLIES => !$left->evaluate($labelToValue) || $right->evaluate($labelToValue),
            self::EQ => !($left->evaluate($labelToValue) xor $right->evaluate($labelToValue)),
            self::NOR => !($left->evaluate($labelToValue) || $right->evaluate($labelToValue)),
            self::NAND => !($left->evaluate($labelToValue) && $right->evaluate($labelToValue))
        };
    }

    public function getPrecedence(): int
    {
        return match ($this) {
            self::OR => 40,
            self::AND => 30,
            self::XOR => 50,
            self::IMPLIES => 70,
            self::EQ => 60,
            self::NOR => 20,
            self::NAND => 10
        };
    }

    public function toString(): string
    {
        return match ($this) {
            self::OR => '⋁',
            self::AND => '⋀',
            self::XOR => '⊕',
            self::IMPLIES => '→',
            self::EQ => '↔',
            self::NOR => '⭣',
            self::NAND => '|'
        };
    }
}
