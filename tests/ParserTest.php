<?php

use PHPUnit\Framework\TestCase;
use StreetCsv\Config;
use StreetCsv\Exception\UnRecognisedFormat;
use StreetCsv\Parser;

class ParserTest extends TestCase
{
    /**
     * @throws UnRecognisedFormat
     */
    public function testSinglePersonBasic()
    {
        $parser = new Parser(new Config());
        $results = $parser->parseEntry('Mr Aaron Aaronson');

        $this->assertCount(1, $results);
        $this->assertSame('Mr', $results[0]['title']);
        $this->assertSame('Aaron', $results[0]['first_name']);
        $this->assertSame(null, $results[0]['initial']);
        $this->assertSame('Aaronson', $results[0]['last_name']);

        $results = $parser->parseEntry('Ms Evie Björnsdóttir');

        $this->assertCount(1, $results);
        $this->assertSame('Ms', $results[0]['title']);
        $this->assertSame('Evie', $results[0]['first_name']);
        $this->assertSame(null, $results[0]['initial']);
        $this->assertSame('Björnsdóttir', $results[0]['last_name']);
    }

    /**
     * @throws UnRecognisedFormat
     */
    public function testSinglePersonInitial(): void
    {
        $parser = new Parser(new Config());
        $results = $parser->parseEntry('Mr A. Aaronson');

        $this->assertCount(1, $results);
        $this->assertSame('Mr', $results[0]['title']);
        $this->assertSame(null, $results[0]['first_name']);
        $this->assertSame('A', $results[0]['initial']);
        $this->assertSame('Aaronson', $results[0]['last_name']);

        $results = $parser->parseEntry('Ms E Björnsdóttir');

        $this->assertCount(1, $results);
        $this->assertSame('Ms', $results[0]['title']);
        $this->assertSame(null, $results[0]['first_name']);
        $this->assertSame('E', $results[0]['initial']);
        $this->assertSame('Björnsdóttir', $results[0]['last_name']);
    }
    /**
     * @throws UnRecognisedFormat
     */
    public function testExtraCharacters(): void
    {
        $parser = new Parser(new Config());
        $results = $parser->parseEntry(' Mr A. Aaronson;');

        $this->assertCount(1, $results);
        $this->assertSame('Mr', $results[0]['title']);
        $this->assertSame(null, $results[0]['first_name']);
        $this->assertSame('A', $results[0]['initial']);
        $this->assertSame('Aaronson', $results[0]['last_name']);
    }

    /**
     * @throws UnRecognisedFormat
     */
    public function testMarriedCoupleWithNoFirstName(): void
    {
        $parser = new Parser(new Config());
        $results = $parser->parseEntry('Mr and Mrs Aaronson');

        $this->assertCount(2, $results);

        $this->assertSame('Mr', $results[0]['title']);
        $this->assertSame(null, $results[0]['first_name']);
        $this->assertSame(null, $results[0]['initial']);
        $this->assertSame('Aaronson', $results[0]['last_name']);

        $this->assertSame('Mrs', $results[1]['title']);
        $this->assertSame(null, $results[1]['first_name']);
        $this->assertSame(null, $results[1]['initial']);
        $this->assertSame('Aaronson', $results[1]['last_name']);

        $results = $parser->parseEntry('Mr and Mrs Björnsson');

        $this->assertCount(2, $results);

        $this->assertSame('Mr', $results[0]['title']);
        $this->assertSame(null, $results[0]['first_name']);
        $this->assertSame(null, $results[0]['initial']);
        $this->assertSame('Björnsson', $results[0]['last_name']);

        $this->assertSame('Mrs', $results[1]['title']);
        $this->assertSame(null, $results[1]['first_name']);
        $this->assertSame(null, $results[1]['initial']);
        $this->assertSame('Björnsson', $results[1]['last_name']);
    }
}