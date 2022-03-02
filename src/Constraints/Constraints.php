<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Constraints;

interface Constraints
{
    /**
     * @param array<string, bool> $values
     */
    public function isSatisfy(array $values): bool;
}
