<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Parser;

class Token
{
    public const VARIABLE = 'VAR';

    public const OPERATOR = 'OP';

    public const BRACKET_LEFT = '(';

    public const BRACKET_RIGHT = ')';

    private string $type;

    private mixed $payload;

    public function __construct(string $type, mixed $payload)
    {
        $this->type = $type;
        $this->payload = $payload;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPayload(): mixed
    {
        return $this->payload;
    }
}
