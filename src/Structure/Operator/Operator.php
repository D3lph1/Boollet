<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Structure\Operator;

interface Operator
{
    /**
     * @return array<string>
     */
    public function getAliases(): array;

    /**
     * Return operator's precedence. Lower number corresponds to higher priority.
     */
    public function getPrecedence(): int;

    public function toString(): string;
}
