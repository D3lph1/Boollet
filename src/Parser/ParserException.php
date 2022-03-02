<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Parser;

use Exception;

class ParserException extends Exception
{
    public static function missingLeftBracket(): self
    {
        return new self("Missing left bracket");
    }

    public static function missingRightBracket(): self
    {
        return new self("Missing right bracket");
    }
}
