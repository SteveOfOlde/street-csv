<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class HelloWorld extends TestCase
{
    public function testProvePhpunitWorks(): void
    {
        $this->assertSame('hello, world', 'hello, world');
    }
}