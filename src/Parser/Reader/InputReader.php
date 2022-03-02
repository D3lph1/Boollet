<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Parser\Reader;

interface InputReader
{
    /**
     * @return string|null Next input stream character or null in case of EOF.
     */
    public function read(): ?string;
}
