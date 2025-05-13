<?php

use PHPUnit\Framework\TestCase;
use StreetCsv\Config;
use StreetCsv\DTO\Person;
use StreetCsv\Exception\UnRecognisedFormat;
use StreetCsv\Parser;

class ParserTest extends TestCase
{
    /**
     * @throws UnRecognisedFormat
     */
    public function testSinglePersonBasic(): void
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

        $results = $parser->parseEntry('Mrs Evie Björnsdóttir');

        $this->assertCount(1, $results);
        $this->assertSame('Mrs', $results[0]['title']);
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
    public function testMister(): void
    {
        $parser = new Parser(new Config());
        $results = $parser->parseEntry('Mister A. Aaronson');

        $this->assertCount(1, $results);
        $this->assertSame('Mister', $results[0]['title']);
        $this->assertSame(null, $results[0]['first_name']);
        $this->assertSame('A', $results[0]['initial']);
        $this->assertSame('Aaronson', $results[0]['last_name']);
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
    public function testHeteroMarriedCoupleWithNoFirstName(): void
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

    /**
     * @throws UnRecognisedFormat
     */
    public function testSameSexCouplesWithNoFirstName(): void
    {
        $parser = new Parser(new Config());
        $results = $parser->parseEntry('Mr and Mr Aaronson');

//        print_r($results);
//        exit;

        $this->assertCount(2, $results);

        $this->assertSame('Mr', $results[0]['title']);
        $this->assertSame(null, $results[0]['first_name']);
        $this->assertSame(null, $results[0]['initial']);
        $this->assertSame('Aaronson', $results[0]['last_name']);

        $this->assertSame('Mr', $results[1]['title']);
        $this->assertSame(null, $results[1]['first_name']);
        $this->assertSame(null, $results[1]['initial']);
        $this->assertSame('Aaronson', $results[1]['last_name']);

        $results = $parser->parseEntry('Mrs and Mrs Björnsdóttir');

        $this->assertCount(2, $results);

        $this->assertSame('Mrs', $results[0]['title']);
        $this->assertSame(null, $results[0]['first_name']);
        $this->assertSame(null, $results[0]['initial']);
        $this->assertSame('Björnsdóttir', $results[0]['last_name']);

        $this->assertSame('Mrs', $results[1]['title']);
        $this->assertSame(null, $results[1]['first_name']);
        $this->assertSame(null, $results[1]['initial']);
        $this->assertSame('Björnsdóttir', $results[1]['last_name']);
    }

    /**
     * @throws UnRecognisedFormat
     */
    public function testSameSexCouplesWithDifferentNames(): void
    {
        $parser = new Parser(new Config());
        $results = $parser->parseEntry('Mr Baron Baronson and Mr Aaron Aaronson');

        $this->assertCount(2, $results);

        $this->assertSame('Mr', $results[0]['title']);
        $this->assertSame('Baron', $results[0]['first_name']);
        $this->assertSame(null, $results[0]['initial']);
        $this->assertSame('Baronson', $results[0]['last_name']);

        $this->assertSame('Mr', $results[1]['title']);
        $this->assertSame('Aaron', $results[1]['first_name']);
        $this->assertSame(null, $results[1]['initial']);
        $this->assertSame('Aaronson', $results[1]['last_name']);
    }
    /**
     * @throws UnRecognisedFormat
     */
    public function testDoctors(): void
    {
        $parser = new Parser(new Config());

        $results = $parser->parseEntry('Dr Baron Baronson');

        $this->assertCount(1, $results);

        $this->assertSame('Dr', $results[0]['title']);
        $this->assertSame('Baron', $results[0]['first_name']);
        $this->assertSame(null, $results[0]['initial']);
        $this->assertSame('Baronson', $results[0]['last_name']);

        $results = $parser->parseEntry('Dr Baron Baronson and Mr Aaron Aaronson');

        $this->assertCount(2, $results);

        $this->assertSame('Dr', $results[0]['title']);
        $this->assertSame('Baron', $results[0]['first_name']);
        $this->assertSame(null, $results[0]['initial']);
        $this->assertSame('Baronson', $results[0]['last_name']);

        $this->assertSame('Mr', $results[1]['title']);
        $this->assertSame('Aaron', $results[1]['first_name']);
        $this->assertSame(null, $results[1]['initial']);
        $this->assertSame('Aaronson', $results[1]['last_name']);

        $results = $parser->parseEntry('Dr B. Björnsdóttir & Mrs Debbie Cartwright');
        $this->assertCount(2, $results);

        $this->assertSame('Dr', $results[0]['title']);
        $this->assertSame(null, $results[0]['first_name']);
        $this->assertSame('B', $results[0]['initial']);
        $this->assertSame('Björnsdóttir', $results[0]['last_name']);

        $this->assertSame('Mrs', $results[1]['title']);
        $this->assertSame('Debbie', $results[1]['first_name']);
        $this->assertSame(null, $results[1]['initial']);
        $this->assertSame('Cartwright', $results[1]['last_name']);

        $results = $parser->parseEntry('Dr & Mrs Björnsdóttir');
        $this->assertCount(2, $results);

        $this->assertSame('Dr', $results[0]['title']);
        $this->assertSame(null, $results[0]['first_name']);
        $this->assertSame(null, $results[0]['initial']);
        $this->assertSame('Björnsdóttir', $results[0]['last_name']);

        $this->assertSame('Mrs', $results[1]['title']);
        $this->assertSame(null, $results[1]['first_name']);
        $this->assertSame(null, $results[1]['initial']);
        $this->assertSame('Björnsdóttir', $results[1]['last_name']);
    }

    /**
     * @throws UnRecognisedFormat
     */
    public function testProfessors(): void
    {
        $parser = new Parser(new Config());

        $results = $parser->parseEntry('Prof Baron Baronson');

        $this->assertCount(1, $results);

        $this->assertSame('Prof', $results[0]['title']);
        $this->assertSame('Baron', $results[0]['first_name']);
        $this->assertSame(null, $results[0]['initial']);
        $this->assertSame('Baronson', $results[0]['last_name']);
    }

    /**
     * @throws UnRecognisedFormat
     */
    public function testDoubleBarrel(): void
    {
        $parser = new Parser(new Config());

        $results = $parser->parseEntry('Mrs Evie Björnsdóttir-Aaronson');

        $this->assertCount(1, $results);

        $this->assertSame('Mrs', $results[0]['title']);
        $this->assertSame('Evie', $results[0]['first_name']);
        $this->assertSame(null, $results[0]['initial']);
        $this->assertSame('Björnsdóttir-Aaronson', $results[0]['last_name']);
    }
}