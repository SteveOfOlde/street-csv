<?php

use PHPUnit\Framework\TestCase;
use StreetCsv\Config;
use StreetCsv\Parser;

class ParserTest extends TestCase
{
    public function testSinglePersonBasic()
    {
        $parser = new Parser(new Config());
        $results = $parser->parseEntry('Mr Aaron Aaronson');

        $this->assertCount(1, $results);
        $this->assertSame('Mr', $results[0]['title']);
        $this->assertSame('Aaron', $results[0]['first_name']);
        $this->assertSame(null, $results[0]['initial']);
        $this->assertSame('Aaronson', $results[0]['last_name']);

        $results = $parser->parseEntry('Ms Evie Eviedotir');

        $this->assertCount(1, $results);
        $this->assertSame('Ms', $results[0]['title']);
        $this->assertSame('Evie', $results[0]['first_name']);
        $this->assertSame(null, $results[0]['initial']);
        $this->assertSame('Eviedotir', $results[0]['last_name']);
    }
}