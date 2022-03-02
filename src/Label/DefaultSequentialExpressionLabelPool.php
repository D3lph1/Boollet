<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Label;

use D3lph1\Boollet\Label\ExpressionLabelPool;
use D3lph1\Boollet\Label\ResettableVariableLabelPool;
use D3lph1\Boollet\Structure\Expression\Expression;
use Generator;

class DefaultSequentialExpressionLabelPool extends ExpressionLabelPool implements ResettableVariableLabelPool
{
    private Generator $generator;

    public function __construct()
    {
        $this->reset();
    }

    public function getLabelFor(?Expression $expression): string
    {
        $return = $this->generator->current();
        $this->generator->next();

        return $return;
    }

    private function generate(): Generator
    {
        for ($i = 0;; $i++) {
            for ($code = ord('A'); $code <= ord('Z'); $code++) {
                if ($i === 0) {
                    yield chr($code);
                } else {
                    yield chr($code) . $i;
                }
            }
        }

        // Unreachable
        return;
    }

    public function reset(): void
    {
        $this->generator = $this->generate();
    }
}
