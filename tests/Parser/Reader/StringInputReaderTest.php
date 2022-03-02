<?php
declare(strict_types=1);

namespace D3lph1\Boollet\Tests\Parser\Reader;

use D3lph1\Boollet\Parser\Reader\StringInputReader;
use PHPUnit\Framework\TestCase;

class StringInputReaderTest extends TestCase
{
    public function test(): void
    {
        $reader = new StringInputReader('  word ( *+ ⋁•→⇒⭣≡↔⇔');

        self::assertEquals(' ', $reader->read());
        self::assertEquals(' ', $reader->read());
        self::assertEquals('w', $reader->read());
        self::assertEquals('o', $reader->read());
        self::assertEquals('r', $reader->read());
        self::assertEquals('d', $reader->read());
        self::assertEquals(' ', $reader->read());
        self::assertEquals('(', $reader->read());
        self::assertEquals(' ', $reader->read());
        self::assertEquals('*', $reader->read());
        self::assertEquals('+', $reader->read());
        self::assertEquals(' ', $reader->read());
        self::assertEquals('⋁', $reader->read());
        self::assertEquals('•', $reader->read());
        self::assertEquals('→', $reader->read());
        self::assertEquals('⇒', $reader->read());
        self::assertEquals('⭣', $reader->read());
        self::assertEquals('≡', $reader->read());
        self::assertEquals('↔', $reader->read());
        self::assertEquals('⇔', $reader->read());
        self::assertNull($reader->read());
    }
}
