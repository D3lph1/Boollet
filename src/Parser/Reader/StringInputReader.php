<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Parser\Reader;

use D3lph1\Boollet\Parser\Reader\InputReader;

class StringInputReader implements InputReader
{
    private array $str;

    private int $i = 0;

    public function __construct(string $str)
    {
        $this->str = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    public function read(): ?string
    {
        if ($this->i >= count($this->str)) {
            // EOF
            return null;
        }

        return $this->str[$this->i++];
    }
}
