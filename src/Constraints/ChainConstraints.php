<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Constraints;

class ChainConstraints implements Constraints
{
    /**
     * @var array<Constraints>
     */
    private array $constraints;

    public function __construct(array $constraints)
    {
        $this->constraints = $constraints;
    }

    public function isSatisfy(array $values): bool
    {
        foreach ($this->constraints as $constraint) {
            if (!$constraint->isSatisfy($values)) {
                return false;
            }
        }

        return true;
    }
}
