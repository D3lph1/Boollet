<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Parser;

use D3lph1\Boollet\Parser\Reader\InputReader;
use D3lph1\Boollet\Structure\Expression\Expression;
use D3lph1\Boollet\ValueBinder;

interface Parser
{
    /**
     * @param InputReader $input
     * @return Expression
     * @throws ParserException
     */
    public function parse(InputReader $input): Expression;

    public function setValueBinder(ValueBinder $valueBinder): void;
}
